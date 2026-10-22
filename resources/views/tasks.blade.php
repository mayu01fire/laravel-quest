<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- ページタイトル・操作ボタン --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    今日のタスク
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    タスクの進捗や優先度を管理できます。
                </p>
            </div>

            {{-- 操作ボタン --}}
            <div class="flex flex-wrap items-center gap-3">

                <a href="{{ route('categories.index') }}"
                   class="inline-flex items-center justify-center rounded-lg
                          border border-gray-300 bg-white px-5 py-2.5
                          text-sm font-medium text-gray-700 shadow-sm
                          hover:bg-gray-50">
                    カテゴリ管理
                </a>

                <a href="{{ route('tasks.create') }}"
                   class="inline-flex items-center justify-center gap-2
                          rounded-lg bg-blue-600 px-5 py-2.5
                          text-sm font-medium text-white shadow-sm
                          hover:bg-blue-700">
                    <span aria-hidden="true">＋</span>
                    新しいタスク
                </a>

            </div>
        </div>

        {{-- 成功メッセージ --}}
        @if (session('success'))
            <div role="status"
                 class="mb-6 flex items-center gap-3 rounded-xl
                        border border-green-200 bg-green-50
                        px-5 py-4 text-green-800">

                <span class="text-xl" aria-hidden="true">✓</span>

                <p class="text-sm font-medium">
                    {{ session('success') }}
                </p>

            </div>
        @endif


<div class="mb-6 space-y-4">
    <div class="flex flex-wrap items-center gap-3">

    <a
    href="{{ route('tasks.index', [
        'sort' => request('sort'),
        'search' => request('search')
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
        {{ !request('filter') && !request('priority')
            ? 'bg-blue-600 text-white'
            : 'bg-white text-gray-600 hover:bg-gray-100' }}"
    >
        すべて表示
    </a>

    <a href="{{ route('tasks.index', [
        'filter' => 'pending',
        'sort' => request('sort'),
        'search' => request('search'),
        'category_id' => request('category_id'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('filter') === 'pending'
        ? 'bg-blue-600 text-white'
        : 'bg-white text-gray-600 hover:bg-gray-100' }}"
    >
        未完了だけ
    </a>

    <a href="{{ route('tasks.index', [
        'filter' => request('filter'),
        'sort' => 'priority',
        'search' => request('search'),
        'priority' => request('priority'),
        'category_id' => request('category_id'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('sort') === 'priority'
        ? 'bg-blue-600 text-white'
        : 'bg-white text-gray-600 hover:bg-gray-100' }}"
    >
        優先度順
    </a>
    </div>

    <div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('tasks.index', [
        'filter' => request('filter'),
        'sort' => request('sort'),
        'search' => request('search'),
        'priority' => 2,
        'category_id' => request('category_id'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('priority') === '2'
        ? 'bg-red-600 text-white'
        : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
    >
        高
    </a>

    <a href="{{ route('tasks.index', [
        'filter' => request('filter'),
        'sort' => request('sort'),
        'search' => request('search'),
        'priority' => 1,
        'category_id' => request('category_id'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('priority') === '1'
        ? 'bg-amber-600 text-white'
        : 'bg-amber-100 text-amber-700 hover:bg-amber-200' }}"
    >
        中
    </a>

    <a href="{{ route('tasks.index', [
        'filter' => request('filter'),
        'sort' => request('sort'),
        'search' => request('search'),
        'priority' => 0,
        'category_id' => request('category_id'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('priority') === '0'
        ? 'bg-green-600 text-white'
        : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
    >
        低
    </a>

    <a href="{{ route('tasks.index', [
        'filter' => request('filter'),
        'sort' => request('sort'),
        'search' => request('search'),
    ]) }}"
    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition
    {{ request('priority') === null || request('priority') === ''
        ? 'bg-blue-600 text-white'
        : 'bg-white text-gray-600 hover:bg-gray-100' }}"
    >
        すべて
    </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">全タスク</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">
            {{ $totalCount }}
        </p>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">完了済み</p>
        <p class="mt-2 text-3xl font-bold text-green-600">
            {{ $completedCount }}
        </p>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">未完了</p>
        <p class="mt-2 text-3xl font-bold text-orange-500">
            {{ $pendingCount }}
        </p>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">達成率</p>
        <p class="mt-2 text-3xl font-bold text-blue-600">
            {{ $completionRate }}%
        </p>

        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200">
            <div
                class="h-full rounded-full bg-blue-500"
                style="width: {{ $completionRate }}%">
            </div>
        </div>
    </div>

</div>



<div class="mt-8 rounded-xl bg-white p-6 shadow-sm">

    <h2 class="mb-6 text-lg font-semibold text-gray-900">
        タスクを検索・絞り込み
    </h2>

    <form action="{{ route('tasks.index') }}" method="GET"
          class="space-y-5">

        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-gray-700">
                キーワード検索
            </label>

            <input
                type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="タスク名を入力..."
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-gray-700">
                    カテゴリ
                </label>

                <select
                    name="category_id"
                    id="category_id"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">すべてのカテゴリ</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div>
                <label for="sort" class="mb-2 block text-sm font-medium text-gray-700">
                    並び替え
                </label>

                <select
                    name="sort"
                    id="sort"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">標準順</option>
                    <option value="asc" @selected(request('sort') === 'asc')>
                        古い順
                    </option>
                    <option value="desc" @selected(request('sort') === 'desc')>
                        新しい順
                    </option>
                    <option value="priority" @selected(request('sort') === 'priority')>
                        優先度順
                    </option>
                </select>
            </div>

        </div>

        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <input type="hidden" name="priority" value="{{ request('priority') }}">

        <div class="flex justify-end">
            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-6 py-2.5 font-medium text-white hover:bg-blue-700"
            >
                検索する
            </button>
        </div>

    </form>

</div>

<section class="mt-8">
    <h2 class="mb-5 text-xl font-bold text-gray-900">
        タスク一覧
    </h2>

    <div class="space-y-4">
@foreach ($tasks as $task)
<div class="rounded-xl bg-white p-6 shadow-sm">

<div class="flex flex-wrap items-start justify-between gap-4">

    <div>
        @if ($task->completed == 1)
            <h3 class="text-lg font-semibold text-gray-400 line-through">
                {{ $task->title }}
            </h3>
        @else
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $task->title }}
            </h3>
        @endif
    </div>

    <span class="rounded-full px-3 py-1 text-xs font-semibold
        @if ($task->priority == 2)
            bg-red-100 text-red-700
        @elseif ($task->priority == 1)
            bg-amber-100 text-amber-700
        @else
            bg-green-100 text-green-700
        @endif
    ">
        @if ($task->priority == 2)
            高
        @elseif ($task->priority == 1)
            中
        @else
            低
        @endif
    </span>

</div>

<div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-600">

    <span>
        📁 {{ $task->category?->name ?? 'カテゴリなし' }}
    </span>

    <span>
        📅 {{ $task->deadline?->format('Y/m/d') ?? '期限なし' }}
    </span>

    @if ($task->completed == 1)
        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
            完了済み
        </span>
    @else
        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
            未完了
        </span>
    @endif

</div>


<div class="mt-5 flex flex-col gap-4 border-t border-gray-100 pt-4
            sm:flex-row sm:items-center sm:justify-between">

    <p class="text-sm text-gray-500">
        所有者：{{ $task->user->name }}
    </p>

    <div class="flex flex-wrap items-center gap-2">

        <a href="{{ route('tasks.show', $task) }}"
           class="inline-flex items-center rounded-lg border border-gray-300
                  px-4 py-2 text-sm font-medium text-gray-700
                  hover:bg-gray-50">
            詳細
        </a>

        <a href="{{ route('tasks.edit', $task) }}"
           class="inline-flex items-center rounded-lg border border-gray-300
                  px-4 py-2 text-sm font-medium text-gray-700
                  hover:bg-gray-50">
            編集
        </a>

        <form action="{{ route('tasks.destroy', $task) }}"
              method="POST"
              onsubmit="return confirm('このタスクを削除しますか？');">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="rounded-lg border border-red-200 px-4 py-2
                           text-sm font-medium text-red-600 hover:bg-red-50">
                削除
            </button>
        </form>

        <form action="{{ route('tasks.complete', $task) }}"
              method="POST">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2
                           text-sm font-medium text-white hover:bg-blue-700">

                @if ($task->completed == 0)
                    完了する
                @else
                    未完了に戻す
                @endif

            </button>
        </form>
        </div>
    </div>
</div>
@endforeach
    </div>
</section>

    </div>
</x-app-layout>