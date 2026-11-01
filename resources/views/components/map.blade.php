@props(['class' => ''])

@php
    $mapUrl = setting('map_url');
@endphp

<a href="{{ $mapUrl ?: '#' }}" @if ($mapUrl) target="_blank" rel="noopener" @endif {{ $attributes->merge(['class' => 'map-card group block bg-navy-900 '.$class]) }} aria-label="مشاهده موقعیت روی نقشه">
    <img src="{{ asset('images/map.svg') }}" alt="نقشه شهرک صنعتی تجریشی" width="800" height="600" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

    <span class="map-pin">
        <span class="flex flex-col items-center">
            <span class="flex size-11 items-center justify-center rounded-full bg-brand text-navy-950 shadow-lg ring-4 ring-white/80">
                <x-icon name="pin" class="size-6" />
            </span>
            <span class="mt-2 rounded-md bg-white/95 px-2.5 py-1 text-[11px] font-bold text-navy shadow">شهرک صنعتی تجریشی</span>
        </span>
    </span>

    <span class="absolute bottom-2 end-2 rounded-md bg-navy-950/80 px-2 py-1 text-[10px] font-medium text-white opacity-0 transition group-hover:opacity-100">مشاهده روی نقشه</span>
</a>
