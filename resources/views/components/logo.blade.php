@props(['variant' => 'dark', 'href' => null, 'showTagline' => true])

@php
    $isLight = $variant === 'light';
@endphp

<a href="{{ $href ?? route('home') }}" {{ $attributes->merge(['class' => 'group flex items-center gap-2.5']) }} aria-label="{{ setting('site_name', 'پلاستو ماشین') }}">
    <svg class="size-11 shrink-0 transition group-hover:rotate-12" viewBox="0 0 48 48" aria-hidden="true">
        <g transform="translate(24 24)" fill="#f6a21a">
            <rect x="-4.5" y="-23" width="9" height="12" rx="3" />
            <rect x="-4.5" y="11" width="9" height="12" rx="3" />
            <rect x="-23" y="-4.5" width="12" height="9" rx="3" />
            <rect x="11" y="-4.5" width="12" height="9" rx="3" />
            <rect x="-4.5" y="-23" width="9" height="12" rx="3" transform="rotate(45)" />
            <rect x="-4.5" y="11" width="9" height="12" rx="3" transform="rotate(45)" />
            <rect x="-4.5" y="-23" width="9" height="12" rx="3" transform="rotate(-45)" />
            <rect x="-4.5" y="11" width="9" height="12" rx="3" transform="rotate(-45)" />
        </g>
        <circle cx="24" cy="24" r="15" fill="#0b2949" />
        <circle cx="24" cy="24" r="10.5" fill="none" stroke="#f6a21a" stroke-width="2" opacity=".55" />
        <circle cx="24" cy="24" r="4.5" fill="#ffffff" />
    </svg>

    <span class="flex flex-col leading-tight">
        <span class="text-lg font-black tracking-tight sm:text-xl {{ $isLight ? 'text-white' : 'text-navy' }}">{{ setting('site_name', 'پلاستو ماشین') }}</span>
        @if ($showTagline)
            <span class="mt-1 text-[10px] font-medium sm:text-[11px] {{ $isLight ? 'text-white/60' : 'text-slate-500' }}">{{ setting('tagline', 'خرید و فروش ماشین‌آلات تولید نایلون') }}</span>
        @endif
    </span>
</a>
