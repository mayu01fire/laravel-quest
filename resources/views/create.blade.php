
<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                新しいタスクを登録
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                タスクの内容や優先度、期限を設定します。
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm sm:p-8">

            <form action="{{ route('tasks.store') }}"
                  method="POST"
                  class="space-y-6">
                @csrf

                <!-- タスク名 -->
                <div>
                    <label for="title"
                           class="mb-2 block text-sm font-medium text-gray-700">
                        タスク名
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="例：Laravelの学習を1時間する"
                        required
                        class="w-full rounded-lg border-gray-300
                               focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- カテゴリ・優先度 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                    <div>
                        <label for="category_id"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            カテゴリ
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">カテゴリなし</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id') == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            優先度
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="0"
                                @selected(old('priority', '1') == '0')>
                                低
                            </option>

                            <option value="1"
                                @selected(old('priority', '1') == '1')>
                                中
                            </option>

                            <option value="2"
                                @selected(old('priority', '1') == '2')>
                                高
                            </option>
                        </select>

                        @error('priority')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <!-- 期限 -->
                <div>
                    <label for="deadline"
                           class="mb-2 block text-sm font-medium text-gray-700">
                        期限
                    </label>

                    <input
                        type="date"
                        id="deadline"
                        name="deadline"
                        value="{{ old('deadline') }}"
                        class="w-full rounded-lg border-gray-300
                               focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('deadline')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 操作ボタン -->
                <div class="flex flex-wrap justify-end gap-3
                            border-t border-gray-100 pt-6">

                    <a href="{{ route('tasks.index') }}"
                       class="inline-flex items-center justify-center
                              rounded-lg border border-gray-300
                              px-5 py-2.5 text-sm font-medium
                              text-gray-700 hover:bg-gray-50">
                        キャンセル
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5
                               text-sm font-medium text-white
                               hover:bg-blue-700">
                        タスクを登録
                    </button>

                </div>

            </form>
        </div>
    </div>
</x-app-layout>
