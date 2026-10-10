<h1>タスク編集</h1>

<form action="{{ route('tasks.update', $task) }}" method="post">
    @csrf
    @method('PUT')

    <input type="text" name="title" value="{{ old('title', $task->title) }}">

@if ($errors->has('title'))
    <p>{{ $errors->first('title') }}</p>
@endif

<select name="priority">
    <option value="0" @selected(old('priority', $task->priority) == 0)>低</option>
    <option value="1" @selected(old('priority', $task->priority) == 1)>中</option>
    <option value="2" @selected(old('priority', $task->priority) == 2)>高</option>
</select>

@if ($errors->has('priority'))
    <p>{{ $errors->first('priority') }}</p>
@endif

    <button>編集</button>
</form>