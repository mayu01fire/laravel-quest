<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Task::class);

        $request->validate([
            'priority' => ['nullable', 'in:0,1,2'],
        ]);

        $filter = $request->input('filter');
        $sort = $request->input('sort');
        $search = $request->input('search');
        $priority = $request->input('priority');

        if ($sort == 'asc') {
            $direction = 'asc';
        } else {
            $direction = 'desc';
        }

        $user = auth()->user();

        $query = $user->tasks()
            ->with('user');

        if ($sort == 'priority') {
            $query->orderBy('priority', 'desc')
              ->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', $direction);
        }


        if ($filter == 'pending') {
            $query->where('completed', 0);
        }

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($priority !== null && $priority !== '') {
            $query->where('priority', $priority);
        }

        $tasks = $query->get();
        
        $completedCount = $user->tasks()
            ->where('completed', 1)
            ->count();

        $pendingCount = $user->tasks()
            ->where('completed', 0)
            ->count();

        $totalCount = $completedCount + $pendingCount;

        if ($totalCount > 0) {
            $completionRate = round($completedCount / $totalCount * 100);
        } else {
            $completionRate = 0;
        }

        return view('tasks', [
            'tasks' => $tasks,
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'totalCount' => $totalCount,
            'completionRate' => $completionRate
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Task::class);

        return view('create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Task::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $user = auth()->user();

        $task = new Task();
        $task->title = $validated['title'];
        $task->priority = $validated['priority'];

        $user->tasks()->save($task);

        return redirect()
        ->route('tasks.index')
        ->with('success', 'タスクを追加しました！');
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);

        return view('edit', ['task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'in:0,1,2'],
        ]);
    
        $task->title = $validated['title'];
        $task->priority = $validated['priority'];
        $task->save();

        return redirect()
        ->route('tasks.index')
        ->with('success', 'タスクを更新しました！');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        
        $task->delete();

        return redirect()
        ->route('tasks.index')
        ->with('success', 'タスクを削除しました！');
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        return view('show', ['task' => $task]);
    }

    public function complete(Task $task)
    {
        Gate::authorize('update', $task);

        if($task->completed == 0){
            $task->completed = 1;
            $message = 'タスクを完了しました！';
        } else{
            $task->completed = 0;
            $message = 'タスクを未完了に戻しました！';
        }

        $task->save();

        return redirect()
        ->route('tasks.index')
        ->with('success', $message);
    }
}