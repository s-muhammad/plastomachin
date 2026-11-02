@props(['service', 'index' => null])

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
    $image = is_array($service)
        ? \App\Models\ServicePage::imageUrl($service['image'] ?? null)
        : $service->image_url;
    $icon = $icons[$slug] ?? 'factory';
@endphp

<a href="{{ route('services.show', $slug) }}" class="group relative flex h-full flex-col overflow-hidden rounded-[1.75rem] border border-slate-200/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1.5 hover:border-brand/60 hover:shadow-[0_28px_60px_-28px_rgba(11,41,73,0.5)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
    <div class="relative aspect-[16/10] overflow-hidden bg-navy-950">
        <img
            src="{{ $image }}"
            alt="{{ $title }}"
            width="1200"
            height="700"
            loading="lazy"
            class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-110"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/25 to-navy-950/5"></div>
        <div class="absolute inset-0 ring-1 ring-inset ring-white/10"></div>

        @if ($index)
            <span class="absolute top-4 start-4 inline-flex size-9 items-center justify-center rounded-full border border-white/20 bg-white/10 text-[11px] font-black text-white backdrop-blur-md">
                {{ fa_num(str_pad((string) $index, 2, '0', STR_PAD_LEFT)) }}
            </span>
        @endif

        <span class="absolute top-4 end-4 flex size-11 items-center justify-center rounded-2xl bg-white/10 text-brand ring-1 ring-inset ring-white/20 backdrop-blur-md transition duration-300 group-hover:bg-brand group-hover:text-navy-950">
            <x-icon :name="$icon" class="size-5" />
        </span>

        <span class="absolute bottom-4 start-4 inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1 text-[10px] font-bold text-white/85 ring-1 ring-inset ring-white/15 backdrop-blur-md">
            <span class="size-1.5 rounded-full bg-brand"></span>
            حوزه‌ی کاری
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5 sm:p-6">
        <h3 class="text-lg font-extrabold leading-8 text-navy transition group-hover:text-brand-dark">{{ $title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-7 text-slate-600">{{ $excerpt }}</p>

        <span class="mt-5 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-dark">
                مشاهده توضیحات
                <x-icon name="arrow-left" class="size-4 transition duration-300 group-hover:-translate-x-1" />
            </span>
            <span class="flex size-9 items-center justify-center rounded-full bg-cream text-navy transition duration-300 group-hover:bg-brand group-hover:text-navy-950">
                <x-icon name="arrow-left" class="size-4" />
            </span>
        </span>
    </div>
</a>
