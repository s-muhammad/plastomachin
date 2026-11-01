@props(['title', 'subtitle' => null, 'breadcrumb' => null])

<section class="relative overflow-hidden bg-navy-950 text-white">
    <div class="absolute inset-0 hero-glow"></div>
    <div class="absolute -end-24 -top-24 size-72 rounded-full border border-white/10"></div>
    <div class="absolute -start-20 bottom-0 size-56 rounded-full border border-white/5"></div>

    <div class="container-site relative py-12 sm:py-16">
        @if ($breadcrumb)
            <nav aria-label="مسیر صفحه" class="mb-4 flex flex-wrap items-center gap-2 text-xs text-white/60">
                <a href="{{ route('home') }}" class="transition hover:text-brand">خانه</a>
                <x-icon name="chevron-left" class="size-3.5" />
                <span class="text-white/85">{{ $breadcrumb }}</span>
            </nav>
        @endif

        <h1 class="max-w-4xl text-2xl font-black leading-[1.6] text-balance sm:text-3xl lg:text-4xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-4 max-w-3xl text-sm leading-7 text-white/70 sm:text-base">{{ $subtitle }}</p>
        @endif

        @if (trim($slot))
            <div class="mt-6">{{ $slot }}</div>
        @endif
    </div>
</section>
