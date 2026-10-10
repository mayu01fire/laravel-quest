<h1>タスク詳細</h1>

@if ($task->completed)
<p>{{$task->title}} 完了</p>
@else
<p>{{$task->title}} 未完了</p>
@endif
