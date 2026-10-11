<h1>今日のタスク</h1>

<a href="{{ route('tasks.index', [
    'sort' => request('sort'),
    'search' => request('search')
]) }}">すべて表示</a>

<a href="{{ route('tasks.index', [
    'filter' => 'pending',
    'sort' => request('sort'),
    'search' => request('search')
]) }}">未完了だけ</a>

<a href="{{ route('tasks.index', [
    'filter' => request('filter'),
    'sort' => 'priority',
    'search' => request('search')
]) }}">優先度順</a>

<p>全タスク：{{ $totalCount }}件</p>
<p>完了済み：{{ $completedCount }}件</p>
<p>未完了：{{ $pendingCount }}件</p>
<p>タスク達成率：{{ $completionRate }}％</p>
<progress value="{{ $completionRate }}" max="100"></progress>

<a href="{{ route('tasks.index', [
    'filter' => request('filter'),
    'sort' => 'asc',
    'search' => request('search')
]) }}">古い順</a>

<a href="{{ route('tasks.index', [
    'filter' => request('filter'),
    'sort' => 'desc',
    'search' => request('search')
]) }}">新しい順</a>

@if (session('success'))
<p>{{ session('success') }}</p>
@endif

<form action="{{ route('tasks.index') }}" method="GET">
    <input type="text" name="search" value="{{ request('search') }}">
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <input type="hidden" name="filter" value="{{ request('filter') }}">

    <button type="submit">検索</button>
</form>

@foreach ($tasks as $task)

    @if ($task->completed == 1)
        <s>{{ $task->title }}</s>
    @else
        {{ $task->title }}
    @endif

    @if ($task->priority == 0)
        優先度：低
    @elseif ($task->priority == 1)
        優先度：中
    @else
        優先度：高
    @endif

    所有者：{{ $task->user->name }}

    <a href="{{ route('tasks.edit', $task) }}">編集</a>
    <a href="{{ route('tasks.show', $task) }}">詳細</a>
   
    <form action="{{ route('tasks.destroy', $task) }}" method="post">
    @csrf
    @method('DELETE')
    <button>削除</button>
    </form>

    <form action="{{ route('tasks.complete', $task) }}" method="post">
    @csrf
    @method('PATCH')
    <button>
        @if ($task->completed == 0)
            完了
        @else
            未完了に戻す
        @endif
    </button>
    </form>

@endforeach