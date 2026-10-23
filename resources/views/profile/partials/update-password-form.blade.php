
<section>
    <header>
        <h2 class="text-xl font-semibold text-gray-900">
            パスワード変更
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            アカウントを安全に保つため、推測されにくいパスワードを設定してください。
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        {{-- 現在のパスワード --}}
        <div>
            <x-input-label
                for="update_password_current_password"
                value="現在のパスワード"
            />

            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />
        </div>

        {{-- 新しいパスワード --}}
        <div>
            <x-input-label
                for="update_password_password"
                value="新しいパスワード"
            />

            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />
        </div>

        {{-- 新しいパスワード（確認） --}}
        <div>
            <x-input-label
                for="update_password_password_confirmation"
                value="新しいパスワード（確認）"
            />

            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        {{-- 保存 --}}
        <div class="flex flex-wrap items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                パスワードを変更
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-green-700"
                    role="status"
                >
                    パスワードを変更しました。
                </p>
            @endif
        </div>
    </form>
</section>
