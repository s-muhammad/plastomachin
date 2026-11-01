<header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-8">
    <button type="button" class="btn md:hidden" data-variant="outline" data-size="icon" data-sidebar-toggle aria-label="باز کردن منو">
        <x-icon name="menu" class="size-4" />
    </button>

    <div class="min-w-0">
        <p class="text-[10px] font-bold text-brand-dark">پنل مدیریت</p>
        <h1 class="truncate text-base font-extrabold text-navy">@yield('title', 'داشبورد')</h1>
    </div>

    <div class="ms-auto flex items-center gap-2">
        <a href="{{ route('admin.messages.index') }}" class="btn relative" data-variant="ghost" data-size="icon" aria-label="پیام‌های دریافتی">
            <x-icon name="inbox" class="size-4" />
            @if (($sidebarNewMessages ?? 0) > 0)
                <span class="absolute -top-0.5 -end-0.5 flex size-4 items-center justify-center rounded-full bg-brand text-[9px] font-black text-navy-950">{{ fa_num($sidebarNewMessages) }}</span>
            @endif
        </a>

        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn hidden sm:inline-flex" data-variant="outline" data-size="sm">
            <x-icon name="external" class="size-3.5" />
            مشاهده سایت
        </a>

        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn sm:hidden" data-variant="outline" data-size="icon-sm" aria-label="مشاهده سایت">
            <x-icon name="external" class="size-3.5" />
        </a>
    </div>
</header>
