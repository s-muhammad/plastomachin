@props(['callStyle' => 'navy', 'whatsappLabel' => 'مشاوره در واتساپ', 'showPhone' => false])

@php
    $whatsapp = \App\Support\Site::whatsappUrl(null, 'سلام، برای خرید دستگاه تولید و دوخت نایلون راهنمایی می‌خواهم.');
    $phoneHref = \App\Support\Site::phoneHref();
    $phone = \App\Support\Site::phoneDisplay();
    $callClass = $callStyle === 'outline' ? 'btn-outline-light' : 'btn-navy';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}>
    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn-whatsapp">
        <x-icon name="whatsapp" class="size-5" />
        <span>{{ $whatsappLabel }}</span>
    </a>

    <a href="{{ $phoneHref }}" class="{{ $callClass }}">
        <x-icon name="phone" class="size-5" />
        <span>تماس با ما</span>
        @if ($showPhone)
            <span dir="ltr" class="text-xs font-medium opacity-80">{{ $phone }}</span>
        @endif
    </a>
</div>
