<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 shadow-sm backdrop-blur">
    <div class="container-site flex h-20 items-center justify-between gap-3">
        <x-logo />

        <nav class="hidden items-center gap-7 text-sm font-bold text-navy lg:flex" aria-label="منوی اصلی">
            <a href="{{ route('home') }}" class="relative py-2 transition hover:text-brand-dark {{ request()->routeIs('home') ? 'text-navy after:absolute after:inset-x-0 after:-bottom-0.5 after:h-0.5 after:bg-brand' : '' }}">خانه</a>
            <a href="{{ route('home') }}#services" class="py-2 transition hover:text-brand-dark">حوزه‌های کاری</a>
            <a href="{{ route('home') }}#about" class="py-2 transition hover:text-brand-dark">درباره ما</a>
            <a href="{{ route('blog.index') }}" class="py-2 transition hover:text-brand-dark {{ request()->routeIs('blog.*') ? 'text-brand-dark' : '' }}">مقالات</a>
            <a href="{{ route('faq') }}" class="py-2 transition hover:text-brand-dark {{ request()->routeIs('faq') ? 'text-brand-dark' : '' }}">سوالات متداول</a>
            <a href="{{ route('contact') }}" class="py-2 transition hover:text-brand-dark {{ request()->routeIs('contact') ? 'text-brand-dark' : '' }}">تماس با ما</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- <a href="{{ \App\Support\Site::phoneHref() }}" class="hidden items-center gap-2 rounded-lg px-2 py-2 text-sm font-extrabold text-navy transition hover:text-brand-dark sm:flex" aria-label="تماس تلفنی">
                <x-icon name="phone" class="size-4 text-brand-dark" />
                <span dir="ltr" class="tracking-wide">{{ \App\Support\Site::phoneDisplay() }}</span>
            </a> --}}

            <a href="{{ route('contact') }}" class="btn-brand hidden !rounded-lg !px-4 !py-2.5 md:inline-flex">
                <x-icon name="phone" class="size-4" />
                تماس با ما
            </a>

            <button type="button" class="inline-flex size-10 items-center justify-center rounded-lg border border-slate-200 text-navy transition hover:border-brand hover:text-brand-dark lg:hidden" data-mobile-menu-open aria-expanded="false" aria-controls="mobile-menu" aria-label="باز کردن منو">
                <x-icon name="menu" class="size-5" />
            </button>
        </div>
    </div>
</header>

<!-- Mobile menu: kept outside the sticky header so fixed positioning is relative to the viewport -->
<div id="mobile-menu" class="fixed inset-0 z-50 hidden lg:hidden" data-mobile-menu role="dialog" aria-modal="true" aria-label="منوی موبایل">
    <div class="absolute inset-0 bg-navy-950/60 backdrop-blur-sm" data-mobile-menu-close></div>

    <div class="absolute inset-y-0 end-0 flex w-[86%] max-w-sm flex-col bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <x-logo :show-tagline="false" />
            <button type="button" class="flex size-9 items-center justify-center rounded-lg border border-slate-200 text-navy" data-mobile-menu-close aria-label="بستن منو">
                <x-icon name="close" class="size-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-5 py-4" aria-label="منوی موبایل">
            <ul class="space-y-1 text-sm font-bold text-navy">
                <li><a href="{{ route('home') }}" class="block rounded-lg px-3 py-3 hover:bg-cream">خانه</a></li>
                <li><a href="{{ route('home') }}#services" class="block rounded-lg px-3 py-3 hover:bg-cream">حوزه‌های کاری</a></li>
                <li><a href="{{ route('home') }}#about" class="block rounded-lg px-3 py-3 hover:bg-cream">درباره ما</a></li>
                <li><a href="{{ route('blog.index') }}" class="block rounded-lg px-3 py-3 hover:bg-cream">مقالات</a></li>
                <li><a href="{{ route('faq') }}" class="block rounded-lg px-3 py-3 hover:bg-cream">سوالات متداول</a></li>
                <li><a href="{{ route('contact') }}" class="block rounded-lg px-3 py-3 hover:bg-cream">تماس با ما</a></li>
            </ul>

            <div class="mt-6 rounded-xl bg-cream p-4">
                <p class="text-xs font-bold text-slate-500">شماره تماس</p>
                <a href="{{ \App\Support\Site::phoneHref() }}" dir="ltr" class="mt-1 block text-lg font-black text-navy">{{ \App\Support\Site::phoneDisplay() }}</a>
            </div>
        </nav>

        <div class="border-t border-slate-100 p-5">
            <x-contact-buttons call-style="navy" class="flex-col !items-stretch" />
        </div>
    </div>
</div>
