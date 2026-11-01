@props(['kicker', 'title'])

<div class="mx-auto max-w-2xl text-center">
    <span class="section-kicker">
        <span class="h-px w-6 bg-brand"></span>
        {{ $kicker }}
        <span class="h-px w-6 bg-brand"></span>
    </span>
    <h2 class="section-title">{{ $title }}</h2>
    @if (trim($slot))
        <div class="mt-3 text-sm leading-7 text-slate-600">{{ $slot }}</div>
    @endif
</div>
