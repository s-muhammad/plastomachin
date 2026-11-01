@extends('layouts.site')

@section('content')
    <x-page-hero
        title="مقالات و راهنمای خرید ماشین‌آلات نایلون"
        breadcrumb="مقالات"
        subtitle="نکته‌ها و راهنمایی‌های کاربردی برای انتخاب، خرید و نگهداری دستگاه‌های تولید، دوخت، برش و چاپ نایلون."
    />

    <section class="bg-cream py-12 sm:py-16">
        <div class="container-site">
            @if ($posts->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
                    <p class="text-sm text-slate-500">هنوز مقاله‌ای منتشر نشده است.</p>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" width="1200" height="700" loading="lazy" class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105">
                            </a>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center gap-2 text-[11px] font-medium text-slate-400">
                                    <x-icon name="clock" class="size-3.5" />
                                    <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->jalali_date }}</time>
                                </div>

                                <h2 class="mt-3 text-lg font-extrabold leading-8 text-navy">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="transition hover:text-brand-dark">{{ $post->title }}</a>
                                </h2>

                                <p class="mt-2 flex-1 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 130) }}</p>

                                <a href="{{ route('blog.show', $post->slug) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-dark">
                                    ادامه‌ی مقاله
                                    <x-icon name="arrow-left" class="size-4 transition group-hover:-translate-x-1" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{ $posts->links() }}
            @endif
        </div>
    </section>
@endsection
