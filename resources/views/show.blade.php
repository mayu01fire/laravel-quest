
<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        <!-- ページタイトル -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                タスク詳細
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                登録済みのタスク情報を確認できます。
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm sm:p-8">

            <!-- ステータス -->
            <div class="mb-6 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">
                    タスク情報
                </h2>

                @if ($task->completed)
                    <span class="rounded-full bg-green-100 px-3 py-1
                                 text-sm font-medium text-green-700">
                        完了済み
                    </span>
                @else
                    <span class="rounded-full bg-yellow-100 px-3 py-1
                                 text-sm font-medium text-yellow-700">
                        未完了
                    </span>
                @endif
            </div>

            <!-- タスク名 -->
            <div class="mb-8 border-b border-gray-100 pb-6">
                <p class="mb-2 text-sm text-gray-500">
                    タスク名
                </p>

                <p class="break-words text-xl font-semibold text-gray-900">
                    {{ $task->title }}
                </p>
            </div>

            <!-- タスク詳細情報 -->
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">

                <!-- カテゴリ -->
                <div>
                    <p class="mb-2 text-sm text-gray-500">
                        カテゴリ
                    </p>

                    <p class="font-medium text-gray-900">
                        {{ $task->category?->name ?? 'カテゴリなし' }}
                    </p>
                </div>

                <!-- 優先度 -->
                <div>
                    <p class="mb-2 text-sm text-gray-500">
                        優先度
                    </p>

                    @if ($task->priority == 2)
                        <span class="inline-block rounded-full bg-red-100
                                     px-3 py-1 text-sm font-medium text-red-700">
                            高
                        </span>
                    @elseif ($task->priority == 1)
                        <span class="inline-block rounded-full bg-yellow-100
                                     px-3 py-1 text-sm font-medium text-yellow-700">
                            中
                        </span>
                    @else
                        <span class="inline-block rounded-full bg-green-100
                                     px-3 py-1 text-sm font-medium text-green-700">
                            低
                        </span>
                    @endif
                </div>

                <!-- 期限 -->
                <div>
                    <p class="mb-2 text-sm text-gray-500">
                        期限
                    </p>

                    <p class="font-medium text-gray-900">
                        {{ $task->deadline?->format('Y/m/d') ?? '期限なし' }}
                    </p>
                </div>

                <!-- ステータス -->
                <div>
                    <p class="mb-2 text-sm text-gray-500">
                        ステータス
                    </p>

                    <p class="font-medium text-gray-900">
                        {{ $task->completed ? '完了済み' : '未完了' }}
                    </p>
                </div>

            </div>

            <!-- 操作ボタン -->
            <div class="mt-8 flex flex-wrap justify-end gap-3
                        border-t border-gray-100 pt-6">

                <a href="{{ route('tasks.index') }}"
                   class="inline-flex items-center justify-center
                          rounded-lg border border-gray-300 px-5 py-2.5
                          text-sm font-medium text-gray-700
                          hover:bg-gray-50">
                    一覧に戻る
                </a>

                <a href="{{ route('tasks.edit', $task) }}"
                   class="inline-flex items-center justify-center
                          rounded-lg bg-blue-600 px-6 py-2.5
                          text-sm font-medium text-white
                          hover:bg-blue-700">
                    編集する
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
