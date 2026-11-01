@extends('layouts.site')

@section('content')
    <x-page-hero :title="$post->title" :breadcrumb="$post->title" :subtitle="$post->excerpt" />

    <section class="bg-white py-10 sm:py-14">
        <div class="container-site">
            <div class="grid gap-8 lg:grid-cols-12">
                <article class="lg:col-span-8">
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <x-icon name="clock" class="size-4 text-brand-dark" />
                            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->jalali_date }}</time>
                        </span>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('blog.index') }}" class="font-bold text-navy transition hover:text-brand-dark">مقالات</a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" width="1200" height="700" loading="eager" class="aspect-[16/9] w-full object-cover">
                    </div>

                    <div class="rich-text mt-7">
                        {!! $post->body !!}
                    </div>

                    <div class="mt-10 rounded-2xl bg-cream p-5 sm:p-6">
                        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                            <div>
                                <h2 class="text-lg font-extrabold text-navy">برای انتخاب دستگاه مشاوره می‌خواهید؟</h2>
                                <p class="mt-1 text-sm text-slate-500">تماس بگیرید تا گزینه‌های مناسب را بررسی کنیم.</p>
                            </div>
                            <x-contact-buttons class="shrink-0" />
                        </div>
                    </div>
                </article>

                <aside class="lg:col-span-4">
                    <div class="space-y-4 lg:sticky lg:top-24">
                        <div class="rounded-2xl bg-navy p-6 text-white shadow-lg">
                            <h2 class="text-lg font-extrabold">مشاوره خرید دستگاه</h2>
                            <p class="mt-2 text-sm leading-7 text-white/60">نوع دستگاه و نیاز خود را بگویید؛ راهنمایی لازم را دریافت می‌کنید.</p>
                            <a href="{{ \App\Support\Site::phoneHref() }}" dir="ltr" class="mt-4 block text-xl font-black text-brand">{{ \App\Support\Site::phoneDisplay() }}</a>
                            <x-contact-buttons call-style="outline" class="mt-4 !flex-col !items-stretch" />
                        </div>

                        @if ($related->isNotEmpty())
                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <h2 class="text-sm font-extrabold text-navy">مطالب مرتبط</h2>
                                <div class="mt-3 space-y-3">
                                    @foreach ($related as $item)
                                        <a href="{{ route('blog.show', $item->slug) }}" class="group flex gap-3">
                                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" width="120" height="90" loading="lazy" class="size-16 shrink-0 rounded-lg object-cover">
                                            <span class="text-xs font-bold leading-6 text-navy transition group-hover:text-brand-dark">{{ $item->title }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <x-schema-article :post="$post" />
@endsection
