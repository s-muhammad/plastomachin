@props(['service'])

@php
    $icons = [
        'دستگاه-تولید-کیسه-زباله' => 'bag',
        'دستگاه-تولید-نایلون' => 'film',
        'دستگاه-دوخت-و-برش-نایلون' => 'cut',
        'دستگاه-چاپ-نایلون' => 'print',
        'دستگاه-دست-دوم-نایلون' => 'refresh',
    ];

    $title = is_array($service) ? ($service['title'] ?? '') : $service->title;
    $slug = is_array($service) ? ($service['slug'] ?? '') : $service->slug;
    $excerpt = is_array($service) ? ($service['excerpt'] ?? '') : $service->excerpt;
    $icon = $icons[$slug] ?? 'factory';
@endphp

<a href="{{ route('services.show', $slug) }}" class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-brand/60 hover:shadow-lg">
    <span class="flex size-14 items-center justify-center rounded-2xl bg-cream text-navy transition group-hover:bg-brand group-hover:text-navy-950">
        <x-icon :name="$icon" class="size-7" />
    </span>

    <h3 class="mt-5 text-lg font-extrabold leading-8 text-navy">{{ $title }}</h3>
    <p class="mt-2 flex-1 text-sm leading-7 text-slate-600">{{ $excerpt }}</p>

    <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-bold text-brand-dark">
        مشاهده توضیحات
        <x-icon name="arrow-left" class="size-4 transition group-hover:-translate-x-1" />
    </span>
</a>
