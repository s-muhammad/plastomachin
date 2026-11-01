@props(['faqs', 'class' => ''])

<div {{ $attributes->merge(['class' => 'divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white px-5 sm:px-7 '.$class]) }}>
    @foreach ($faqs as $faq)
        <details class="faq-item">
            <summary>
                <span>{{ $faq->question }}</span>
                <span class="faq-icon">
                    <x-icon name="plus" class="size-4" />
                </span>
            </summary>
            <div>{!! nl2br(e($faq->answer)) !!}</div>
        </details>
    @endforeach
</div>
