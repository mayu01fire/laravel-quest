
<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- ページタイトル --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                プロフィール設定
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                アカウント情報やセキュリティ設定を管理できます。
            </p>
        </div>

        {{-- 設定フォーム --}}
        <div class="space-y-6">

            {{-- プロフィール情報 --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- パスワード変更 --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- アカウント削除 --}}
            <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
