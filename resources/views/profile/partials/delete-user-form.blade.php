
<section class="space-y-6">
    <header>
        <h2 class="text-xl font-semibold text-red-700">
            アカウント削除
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            アカウントを削除すると、関連するデータも完全に削除される可能性があります。
            この操作は取り消せません。
        </p>
    </header>

    {{-- 削除確認モーダルを開く --}}
    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700"
    >
        アカウントを削除
    </button>

    {{-- 削除確認モーダル --}}
    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="p-6"
        >
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">
                本当にアカウントを削除しますか？
            </h2>

            <p class="mt-3 text-sm text-gray-600">
                アカウントの削除は取り消せません。
                削除を実行する場合は、確認のため現在のパスワードを入力してください。
            </p>

            {{-- パスワード確認 --}}
            <div class="mt-6">
                <x-input-label
                    for="delete_account_password"
                    value="現在のパスワード"
                />

                <x-text-input
                    id="delete_account_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="現在のパスワード"
                    autocomplete="current-password"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />
            </div>

            {{-- 操作ボタン --}}
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <x-secondary-button
                    type="button"
                    x-on:click="$dispatch('close')"
                >
                    キャンセル
                </x-secondary-button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700"
                >
                    アカウントを削除する
                </button>
            </div>
        </form>
    </x-modal>
</section>
