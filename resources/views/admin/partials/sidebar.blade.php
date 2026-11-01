@php
    $newMessages = $sidebarNewMessages ?? 0;

    $items = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'داشبورد', 'icon' => 'grid'],
        ['route' => 'admin.services.index', 'pattern' => 'admin.services.*', 'label' => 'صفحات خدماتی', 'icon' => 'factory'],
        ['route' => 'admin.posts.index', 'pattern' => 'admin.posts.*', 'label' => 'مقالات', 'icon' => 'file'],
        ['route' => 'admin.faqs.index', 'pattern' => 'admin.faqs.*', 'label' => 'سوالات متداول', 'icon' => 'message'],
        ['route' => 'admin.messages.index', 'pattern' => 'admin.messages.*', 'label' => 'پیام‌ها', 'icon' => 'inbox'],
        ['route' => 'admin.seo.index', 'pattern' => 'admin.seo.*', 'label' => 'سئوی صفحات', 'icon' => 'tag'],
        ['route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'label' => 'تنظیمات سایت', 'icon' => 'settings'],
    ];
@endphp

<aside id="sidebar" class="sidebar" data-side="right" data-breakpoint="768" aria-label="منوی پنل مدیریت">
    <nav>
        <header class="border-b border-sidebar-border">
            <div class="px-3 py-4">
                <x-logo variant="light" :href="route('admin.dashboard')" :show-tagline="false" />
                <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1 text-[10px] font-bold text-white/70">
                    <span class="size-1.5 rounded-full bg-brand"></span>
                    پنل مدیریت
                </p>
            </div>
        </header>

        <section>
            <div role="group">
                <h3>مدیریت محتوا</h3>

                <ul>
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['pattern'])) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" />
                                <span>{{ $item['label'] }}</span>

                                @if ($item['route'] === 'admin.messages.index' && $newMessages > 0)
                                    <span class="badge ms-auto" data-variant="primary">{{ fa_num($newMessages) }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div role="group" class="mt-auto">
                <h3>سایت</h3>

                <ul>
                    <li>
                        <a href="{{ route('home') }}" target="_blank" rel="noopener">
                            <x-icon name="external" />
                            <span>مشاهده سایت</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sitemap') }}" target="_blank" rel="noopener">
                            <x-icon name="tag" />
                            <span>نقشه سایت</span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        <footer class="border-t border-sidebar-border">
            <div class="flex items-center gap-2.5 rounded-lg bg-white/5 p-2.5">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand text-sm font-black text-navy-950">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10px] text-white/50" dir="ltr">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="mt-2 flex w-full items-center gap-2 rounded-md p-2 text-sm text-white/70 transition hover:bg-sidebar-accent hover:text-white">
                    <x-icon name="logout" />
                    خروج از حساب
                </button>
            </form>
        </footer>
    </nav>
</aside>
