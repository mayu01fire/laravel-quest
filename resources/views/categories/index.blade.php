
<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                カテゴリ管理
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                タスクを分類するカテゴリを管理します。
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="mb-4 text-lg font-semibold">
                新しいカテゴリを登録
            </h2>

            <form action="{{ route('categories.store') }}"
                  method="POST"
                  class="flex flex-wrap gap-3">
                @csrf

                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="カテゴリ名を入力"
                       required
                       maxlength="255"
                       class="min-w-0 flex-1 rounded-lg border-gray-300">

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700">
                    追加
                </button>
            </form>

            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

        </div>

        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm">

            <h2 class="mb-5 text-lg font-semibold">
                登録済みカテゴリ
            </h2>

            <div class="space-y-4">

@forelse ($categories as $category)
    <div class="border-b border-gray-100 pb-4">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

            <form
                id="category-update-{{ $category->id }}"
                action="{{ route('categories.update', $category) }}"
                method="POST"
                class="min-w-0 flex-1"
            >
                @csrf
                @method('PATCH')

                <input
                    type="text"
                    name="name"
                    value="{{ $category->name }}"
                    required
                    maxlength="255"
                    class="w-full min-w-0 rounded-lg border-gray-300"
                >
            </form>

            <div class="flex items-center justify-end gap-2">

                <button
                    type="submit"
                    form="category-update-{{ $category->id }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50"
                >
                    名前を変更
                </button>

                <form
                    action="{{ route('categories.destroy', $category) }}"
                    method="POST"
                    onsubmit="return confirm('このカテゴリを削除しますか？');"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="rounded-lg border border-red-200 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                        削除
                    </button>
                </form>

            </div>
        </div>
    </div>

                @empty
                    <p class="text-sm text-gray-500">
                        カテゴリはまだ登録されていません。
                    </p>
                @endforelse
            </div>

        </div>

        <a href="{{ route('tasks.index') }}"
           class="mt-6 inline-block text-sm font-medium text-blue-600 hover:underline">
            ← タスク一覧に戻る
        </a>

    </div>
</x-app-layout>
