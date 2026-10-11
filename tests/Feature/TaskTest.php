<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

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

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => '掃除',
            'priority' => 1,
    ]);

        Task::factory()
        ->for($user)
        ->create([
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

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'PHPの勉強',
            'priority' => 2,
    ]);

    Task::factory()
        ->for($user)
        ->create([
            'title' => 'Laravelの勉強',
            'priority' => 2,
    ]);

        Task::factory()
        ->for($user)
        ->create([
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
}
