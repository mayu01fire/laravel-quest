
<section>
    <header>
        <h2 class="text-xl font-semibold text-gray-900">
            基本情報
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            名前とメールアドレスを変更できます。
        </p>
    </header>

    {{-- メール認証の再送信 --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- プロフィール更新 --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- 名前 --}}
        <div>
            <x-input-label for="name" value="名前" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- メールアドレス --}}
        <div>
            <x-input-label for="email" value="メールアドレス" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm text-amber-800">
                        メールアドレスがまだ認証されていません。
                    </p>

                    <button
                        type="submit"
                        form="send-verification"
                        class="mt-2 text-sm font-medium text-blue-600 underline hover:text-blue-700"
                    >
                        認証メールを再送信する
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-700">
                            認証メールを再送信しました。
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- 保存 --}}
        <div class="flex flex-wrap items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                変更を保存
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-green-700"
                    role="status"
                >
                    プロフィールを更新しました。
                </p>
            @endif
        </div>
    </form>
</section>
