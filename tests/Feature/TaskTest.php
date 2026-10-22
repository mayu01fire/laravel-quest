<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_taskcreate(): void
    {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/tasks', [
        'title' => '買い物',
        'priority' => 1,
    ]);

    $response->assertRedirect('/tasks');

    $response->assertSessionHas(
        'success',
        'タスクを追加しました！'
    );

    $this->assertDatabaseHas('tasks', [
        'title' => '買い物',
    ]);
    }

    public function test_task_is_not_created_when_title_is_empty(): void
    {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/tasks', [
        'title' => '',
    ]);

    $response->assertSessionHasErrors('title');

    $this->assertDatabaseMissing('tasks', [
        'title' => '',
    ]);
    }

    public function test_title_with_255_characters_can_be_created(): void
    {
    $user = User::factory()->create();

    $this->actingAs($user);

    $title = str_repeat('あ', 255);

    User::factory()->create();

    $response = $this->post('/tasks', [
        'title' => $title,
        'priority' => 1,
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => $title,
    ]);
    }

    public function test_title_with_256_characters_cannot_be_created(): void
    {
    $user = User::factory()->create();

    $this->actingAs($user);

    $title = str_repeat('あ', 256);

    $response = $this->post('/tasks', [
        'title' => $title,
    ]);

    $response->assertSessionHasErrors('title');

    $this->assertDatabaseMissing('tasks', [
        'title' => $title,
    ]);
    }

    public function test_task_can_be_updated(): void
    {
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
            'priority' => 1,
        ]);

    $this->actingAs(
        $task->user
    );

    $response = $this->patch('/tasks/' . $task->id, [
        'title' => 'スーパーで買い物',
        'priority' => $task->priority,
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'スーパーで買い物',
    ]);

    $this->assertDatabaseMissing('tasks', [
        'title' => '買い物',
    ]);
    }

    public function test_task_is_not_updated_when_title_is_empty(): void
    {
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
        ]);

    $this->actingAs(
        $task->user
    );

    $response = $this->patch('/tasks/' . $task->id, [
        'title' => '',
    ]);

    $response->assertSessionHasErrors('title');

    $this->assertDatabaseHas('tasks', [
        'title' => '買い物',
    ]);

    $this->assertDatabaseMissing('tasks', [
        'title' => '',
    ]);
    }

    public function test_task_can_be_deleted(): void
    {
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
    ]);

    $this->actingAs(
        $task->user
    );

    $response = $this->delete('/tasks/' . $task->id);

    $this->assertDatabaseMissing('tasks', [
        'title' => '買い物',
    ]);

    $pageResponse = $this->get('/tasks');

    $pageResponse->assertDontSee('買い物');
    }

    public function test_task_can_be_completed(): void
    {
    $task = Task::factory()->create([
        'title' => '買い物',
    ]);

    $this->actingAs(
        $task->user
    );

    $response = $this->patch('/tasks/' . $task->id . '/complete');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => 1,
    ]);

    $pageResponse = $this->get('/tasks?filter=pending');

    $pageResponse->assertDontSee('買い物');
    }

    public function test_search(): void
    {
    $user = User::factory()->create();

    Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravelの勉強',
    ]);

    $this->actingAs($user);

    $pageResponse = $this->get('/tasks?search=Laravel');

    $pageResponse->assertSee('Laravelの勉強');
    $pageResponse->assertDontSee('買い物');
    }

    public function test_tasks_can_be_sorted_desc(): void
    {
    $user = User::factory()->create();

    Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravelの勉強',
    ]);

    $this->actingAs($user);

    $pageResponse = $this->get('/tasks?sort=desc');

    $pageResponse->assertSeeInOrder([
        'Laravelの勉強',
        '買い物',
    ]);
    }

    public function test_tasks_can_be_filtered_and_searched(): void
    {
    $user = User::factory()->create();

    Task::factory()
        ->for($user)
        ->create([
            'title' => '買い物',
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravelの勉強',
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravel本を読む',
            'completed' => 1,
    ]);

    $this->actingAs($user);

    $pageResponse = $this->get('/tasks?filter=pending&search=Laravel');

    $pageResponse->assertDontSee('買い物');
    $pageResponse->assertSee('Laravelの勉強');
    $pageResponse->assertDontSee('Laravel本を読む');
    }

    public function test_factory_can_create_ten_tasks(): void
    {
    Task::factory()->count(10)->create();

    $this->assertDatabaseCount('tasks', 10);
    }

    public function test_completed_task_can_be_reverted_to_incomplete(): void
    {
    // Arrange
    $task = Task::factory()->create([
        'title' => '買い物',
        'completed' => 1,
    ]);

    $this->actingAs($task->user);

    // Act
    $response = $this->patch(
        route('tasks.complete', $task)
    );

    // Assert
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => 0,
    ]);
    }

    public function test_no_tasks_displayed_when_no_match_found(): void
    {
    Task::factory()->create([
        'title' => '買い物',
    ]);

    Task::factory()->create([
        'title' => 'Laravelの勉強',
    ]);

    $response = $this->get(
    route(
        'tasks.index',
    [
        'search' => 'Python',
    ]
    )
    );

    $response->assertDontSee('買い物');
    $response->assertDontSee('Laravelの勉強');
    }

    public function test_logged_in_user_cannot_edit_another_users_task(): void
    {
    $mayu = User::factory()->create();
    $sena = User::factory()->create();

    $task = Task::factory()
        ->for($mayu)
        ->create();

    $this->actingAs($sena);

    $response = $this->get(
        route('tasks.edit', $task)
    );

    $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_tasks(): void
    {
        $response = $this->get('/tasks');

        $response->assertRedirect('/login');
    }

    public function test_logged_in_user_cannot_update_another_users_task(): void
    {
        $mayu = User::factory()->create();
        $sena = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
            ]);

        $this->actingAs($sena);

        $response = $this->patch(
            route('tasks.update', $task),
            [
                'title' => '勝手に変更',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => '買い物',
        ]);
    }

    public function test_logged_in_user_cannot_delete_another_users_task(): void
    {
        $mayu = User::factory()->create();
        $sena = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
            ]);

        $this->actingAs($sena);

        $response = $this->delete(
            route('tasks.destroy', $task)
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => '買い物',
        ]);
    }

    public function test_logged_in_user_cannot_complete_another_users_task(): void
    {
        $mayu = User::factory()->create();
        $sena = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
            ]);

        $this->actingAs($sena);

        $response = $this->patch(
            route('tasks.complete', $task)
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => 0,
        ]);
    }

    public function test_logged_in_user_cannot_view_another_users_task(): void
    {
        $mayu = User::factory()->create();
        $sena = User::factory()->create();

        Task::factory()
            ->for($mayu)
            ->create([
                'title' => 'まゆのタスク',
            ]);

        Task::factory()
            ->for($sena)
            ->create([
                'title' => 'せなのタスク',
            ]);

        $this->actingAs($mayu);

        $response = $this->get(
            route('tasks.index')
        );

        $response->assertSee('まゆのタスク');
        $response->assertDontSee('せなのタスク');
    }

    public function test_logged_in_user_can_update_their_own_task(): void
    {
        $mayu = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
                'priority' => 1,
            ]);

        $this->actingAs($mayu);

        $response = $this->patch(
            route('tasks.update', $task),
            [
                'title' => 'Laravelの勉強',
                'priority' => $task->priority,
            ]
        );

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Laravelの勉強',
        ]);
    }

    public function test_logged_in_user_cannot_show_another_users_task(): void
    {
        $mayu = User::factory()->create();
        $sena = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
            ]);

        $this->actingAs($sena);

        $response = $this->get(
            route('tasks.show', $task)
        );

        $response->assertForbidden();
    }

    public function test_user_cannot_update_task_with_invalid_priority(): void
    {
        $mayu = User::factory()->create();

        $task = Task::factory()
            ->for($mayu)
            ->create([
                'title' => '買い物',
                'priority' => 2,
            ]);

        $this->actingAs($mayu);

        $response = $this->put(
            route('tasks.update', $task),
            [
                'title' => 'Laravelの勉強',
                'priority' => 5,
            ]
        );

        $response->assertSessionHasErrors('priority');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => '買い物',
            'priority' => 2,
        ]);
    }

    public function test_tasks_can_be_sorted_priority_desc(): void
    {
    $user = User::factory()->create();

    Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

    Task::factory()->for($user)->create([
            'title' => '掃除',
            'priority' => 1,
    ]);

        Task::factory()->for($user)->create([
            'title' => '買い物',
            'priority' => 0,
    ]);

    $response = $this->actingAs($user)
    ->get(route('tasks.index', [
        'sort' => 'priority'
    ]));

    $response->assertSeeInOrder([
        'Laravelの勉強',
        '掃除',
        '買い物',
    ]);
    }

    public function test_tasks_with_same_priority_are_sorted_by_newest_first(): void
    {
    $user = User::factory()->create();

    Task::factory()->for($user)->create([
            'title' => 'PHPの勉強',
            'priority' => 2,
    ]);

    Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

        Task::factory()->for($user)->create([
            'title' => '掃除',
            'priority' => 1,
    ]);

    $response = $this->actingAs($user)
    ->get(route('tasks.index', [
        'sort' => 'priority'
    ]));

    $response->assertSeeInOrder([
        'Laravelの勉強',
        'PHPの勉強',
        '掃除',
    ]);
    }

    public function test_tasks_can_be_filtered_by_high_priority(): void
    {
    $user = User::factory()->create();

    Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

    Task::factory()->for($user)->create([
            'title' => 'PHPの勉強',
            'priority' => 1,
    ]);

    Task::factory()->for($user)->create([
            'title' => '掃除',
            'priority' => 0,
    ]);

    $response = $this->actingAs($user)
    ->get(route('tasks.index', [
        'sort' => 'priority',
        'priority' => 2
    ]));

    $response->assertSee('Laravelの勉強');

    $response->assertDontSee('PHPの勉強');

    $response->assertDontSee('掃除');
    }

    public function test_tasks_can_be_filtered_by_low_priority(): void
    {
    $user = User::factory()->create();

    Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

    Task::factory()->for($user)->create([
            'title' => 'PHPの勉強',
            'priority' => 1,
    ]);

    Task::factory()->for($user)->create([
            'title' => '掃除',
            'priority' => 0,
    ]);

    $response = $this->actingAs($user)
    ->get(route('tasks.index', [
        'sort' => 'priority',
        'priority' => 0
    ]));

    $response->assertDontSee('Laravelの勉強');

    $response->assertDontSee('PHPの勉強');

    $response->assertSee('掃除');
    }

    public function test_invalid_priority_is_rejected(): void
    {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
    ->get(route('tasks.index', [
        'priority' => 99,
    ]));

    $response->assertSessionHasErrors('priority');
    }

    public function test_user_cannot_create_task_with_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user3 = User::factory()->create();

        $category = Category::create([
            'name' => '仕事',
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user3)
            ->post(route('tasks.store'), [
                'title' => '不正なカテゴリのテスト',
                'priority' => 0,
                'category_id' => $category->id,
            ]);

        $response->assertSessionHasErrors('category_id');

        $this->assertDatabaseMissing('tasks', [
            'title' => '不正なカテゴリのテスト',
            'user_id' => $user3->id,
        ]);
    }

    public function test_user_can_create_task_with_own_category(): void
    {
        $user = User::factory()->create();

        $category = $user->categories()->create([
            'name' => '仕事',
        ]);

        $response = $this->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Laravelの勉強',
                'priority' => 2,
                'category_id' => $category->id,
            ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Laravelの勉強',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_user_cannot_update_task_with_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $categoryA = $userA->categories()->create([
            'name' => '仕事',
        ]);

        $categoryB = $userB->categories()->create([
            'name' => 'プライベート',
        ]);

        $task = Task::factory()->for($userB)->create([
            'title' => '買い物',
            'priority' => 1,
            'category_id' => $categoryB->id,
        ]);

        $response = $this->actingAs($userB)
            ->patch(route('tasks.update', $task), [
                'title' => '買い物',
                'priority' => 1,
                'category_id' => $categoryA->id,
            ]);

        $response->assertSessionHasErrors('category_id');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => $categoryB->id,
        ]);
    }

    public function test_user_can_change_task_category(): void
    {
        $user = User::factory()->create();

        $categoryA = $user->categories()->create([
            'name' => '仕事',
        ]);

        $categoryB = $user->categories()->create([
            'name' => 'プライベート',
        ]);

        $task = Task::factory()->for($user)->create([
            'title' => '買い物',
            'priority' => 1,
            'category_id' => $categoryA->id,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('tasks.update', $task), [
                'title' => '買い物',
                'priority' => 1,
                'category_id' => $categoryB->id,
            ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => $categoryB->id,
        ]);
    }

    public function test_user_can_remove_task_category(): void
    {
        $user = User::factory()->create();

        $category = $user->categories()->create([
            'name' => '仕事',
        ]);

        $task = Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'priority' => 1,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('tasks.update', $task), [
                'title' => 'Laravelの勉強',
                'priority' => 1,
                'category_id' => '',
            ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => null,
        ]);
    }

    public function test_user_can_filter_tasks_by_category(): void
    {
        $user = User::factory()->create();

        $work = $user->categories()->create([
            'name' => '仕事',
        ]);

        $private = $user->categories()->create([
            'name' => 'プライベート',
        ]);

        Task::factory()->for($user)->create([
            'title' => 'Laravelの勉強',
            'category_id' => $work->id,
        ]);

        Task::factory()->for($user)->create([
            'title' => '買い物',
            'category_id' => $private->id,
        ]);

        Task::factory()->for($user)->create([
            'title' => '資料作成',
            'category_id' => $work->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', [
            'category_id' => $work->id,
        ]));

        $response->assertSee('Laravelの勉強');
        $response->assertSee('資料作成');
        $response->assertDontSee('買い物');
    }

    public function test_user_cannot_filter_by_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $categoryA = $userA->categories()->create([
            'name' => '仕事',
        ]);

        $categoryB = $userB->categories()->create([
            'name' => 'プライベート',
        ]);

        $response = $this->actingAs($userB)->get(route('tasks.index', [
            'category_id' => $categoryA->id,
        ]));

        $response->assertSessionHasErrors('category_id');
    }

    public function test_user_can_update_own_category_name(): void
    {
        $user = User::factory()->create();

        $category = $user->categories()->create([
            'name' => '仕事',
        ]);

        $response = $this->actingAs($user)->patch(route('categories.update', $category), [
            'name' => '業務',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '業務',
        ]);
    }

    public function test_user_cannot_update_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $categoryA = $userA->categories()->create([
            'name' => '仕事',
        ]);

        $response = $this->actingAs($userB)->patch(route('categories.update', $categoryA), [
            'name' => '業務',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $categoryA->id,
            'name' => '仕事',
        ]);
    }

    public function test_user_can_delete_own_category_without_deleting_tasks(): void
    {
        $user = User::factory()->create();

        $category = $user->categories()->create([
            'name' => '削除テスト',
        ]);

        $task = new Task();
        $task->title = '削除確認タスク';
        $task->priority = 1;
        $task->category_id = $category->id;

        $user->tasks()->save($task);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => null,
        ]);
    }

    public function test_user_cannot_delete_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $categoryA = $userA->categories()->create([
            'name' => '仕事',
       ]);

        $response = $this->actingAs($userB)->delete(route('categories.destroy', $categoryA));

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $categoryA->id]);
    }

    public function test_user_can_create_task_with_deadline(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => '期限付きタスク',
                'priority' => 1,
                'deadline' => '2026-10-20',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => '期限付きタスク',
            'user_id' => $user->id,
            'deadline' => '2026-10-20 00:00:00',
        ]);

    }

    public function test_user_can_update_task_deadline(): void
    {
        $user = User::factory()->create();

        $task = new Task();
        $task->title = '期限変更テスト';
        $task->priority = 1;
        $task->deadline = '2026-10-20';

        $user->tasks()->save($task);

        $response = $this->actingAs($user)
            ->patch(route('tasks.update', $task), [
                'title' => '期限変更テスト',
                'priority' => 1,
                'deadline' => '2026-10-25',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deadline' => '2026-10-25 00:00:00',
        ]);
    }

    public function test_user_can_remove_task_deadline(): void
    {
        $user = User::factory()->create();

        $task = new Task();
        $task->title = '期限解除テスト';
        $task->priority = 1;
        $task->deadline = '2026-10-20';

        $user->tasks()->save($task);

        $response = $this->actingAs($user)
            ->patch(route('tasks.update', $task), [
                'title' => '期限解除テスト',
                'priority' => 1,
                'deadline' => '',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deadline' => null,
        ]);
    }

    public function test_user_cannot_create_task_with_invalid_deadline(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => '不正な期限テスト',
                'priority' => 1,
                'deadline' => 'abc',
            ]);

        $response->assertSessionHasErrors('deadline');

        $this->assertDatabaseMissing('tasks', [
            'title' => '不正な期限テスト',
            'user_id' => $user->id,
        ]);
    }
}
