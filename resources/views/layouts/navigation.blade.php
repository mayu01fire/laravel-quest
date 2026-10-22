
<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center justify-between">

            <!-- ロゴ・アプリ名 -->
            <div class="flex items-center gap-8">

                <a href="{{ route('tasks.index') }}"
                   class="flex items-center gap-3">

                    <div class="flex h-9 w-9 items-center justify-center
                                rounded-lg bg-blue-600 text-xl font-bold text-white">
                        ✓
                    </div>

                    <span class="text-xl font-bold text-gray-900">
                        TaskFlow
                    </span>
                </a>

                <!-- PC用ナビゲーション -->
                <div class="hidden items-center gap-6 md:flex">

                    <a href="{{ route('tasks.index') }}"
                       @class([
                           'text-sm font-medium transition hover:text-blue-600',
                           'text-blue-600' => request()->routeIs('tasks.index'),
                           'text-gray-600' => !request()->routeIs('tasks.index'),
                       ])>
                        タスク一覧
                    </a>

                    <a href="{{ route('categories.index') }}"
                       @class([
                           'text-sm font-medium transition hover:text-blue-600',
                           'text-blue-600' => request()->routeIs('categories.*'),
                           'text-gray-600' => !request()->routeIs('categories.*'),
                       ])>
                        カテゴリ管理
                    </a>

                    <a href="{{ route('tasks.create') }}"
                       @class([
                           'text-sm font-medium transition hover:text-blue-600',
                           'text-blue-600' => request()->routeIs('tasks.create'),
                           'text-gray-600' => !request()->routeIs('tasks.create'),
                       ])>
                        新規登録
                    </a>

                </div>
            </div>

            <!-- PC用ユーザーメニュー -->
            <div class="hidden items-center md:flex">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg
                                   px-3 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 hover:text-gray-900">

                            {{ Auth::user()->name }}

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="m6 9 6 6 6-6"/>
                            </svg>

                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            プロフィール
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                                ログアウト
                            </x-dropdown-link>
                        </form>

                    </x-slot>

                </x-dropdown>
            </div>

            <!-- スマホ用メニューボタン -->
            <div class="flex items-center md:hidden">

                <button
                    type="button"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-label="メニューを開閉"
                    aria-controls="mobile-navigation"
                    class="rounded-lg p-2 text-gray-600
                           hover:bg-gray-100">

                    <svg class="h-6 w-6"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>

                    </svg>

                </button>
            </div>

        </div>
    </div>

    <!-- スマホ用ナビゲーション -->
    <div id="mobile-navigation"
         x-cloak
         x-show="open"
         class="border-t border-gray-200 md:hidden">

        <div class="space-y-1 px-4 py-3">

            <a href="{{ route('tasks.index') }}"
               class="block rounded-lg px-3 py-2 text-sm
                      font-medium text-gray-700 hover:bg-gray-100">
                タスク一覧
            </a>

            <a href="{{ route('categories.index') }}"
               class="block rounded-lg px-3 py-2 text-sm
                      font-medium text-gray-700 hover:bg-gray-100">
                カテゴリ管理
            </a>

            <a href="{{ route('tasks.create') }}"
               class="block rounded-lg px-3 py-2 text-sm
                      font-medium text-gray-700 hover:bg-gray-100">
                新規登録
            </a>

        </div>

        <div class="border-t border-gray-200 px-4 py-3">

            <p class="px-3 text-sm font-semibold text-gray-900">
                {{ Auth::user()->name }}
            </p>

            <p class="px-3 text-xs text-gray-500">
                {{ Auth::user()->email }}
            </p>

            <a href="{{ route('profile.edit') }}"
               class="mt-3 block rounded-lg px-3 py-2
                      text-sm text-gray-700 hover:bg-gray-100">
                プロフィール
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="w-full rounded-lg px-3 py-2 text-left
                               text-sm text-red-600 hover:bg-red-50">
                    ログアウト
                </button>
            </form>

        </div>
    </div>

</nav>
