<h1>タスク新規登録</h1>

<form action="{{ route('tasks.store') }}" method="post">
    @csrf

    <input type="text" name="title" value="{{ old('title') }}">

@if ($errors->has('title'))
    <p>{{$errors->first('title')}}</p>
@endif

<select name="priority">
    <option value="0">低</option>
    <option value="1" selected>中</option>
    <option value="2">高</option>
</select>

    <button>登録</button>
</form>
