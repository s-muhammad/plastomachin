@extends('layouts.site')

@section('content')
    <x-page-hero :title="$service->h1" :breadcrumb="$service->title" :subtitle="$service->excerpt">
        <x-contact-buttons call-style="outline" />
    </x-page-hero>

    <section class="bg-white py-12 sm:py-16">
        <div class="container-site">
            <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-8">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="1200" height="700" loading="eager" class="aspect-[16/9] w-full object-cover">
                    </div>

                    <article class="rich-text mt-8">
                        {!! $service->body !!}
                    </article>

                    <!-- New and used -->
                    <div class="mt-10 rounded-2xl border border-slate-200 bg-cream p-5 sm:p-7">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <span class="text-xs font-bold text-brand-dark">بدون اعلام قیمت روی سایت</span>
                                <h2 class="mt-1 text-xl font-extrabold text-navy">نو و دست‌دوم موجود است</h2>
                            </div>
                            <p class="text-xs leading-6 text-slate-500 sm:text-end">مدل، ظرفیت و شرایط هر دستگاه با تماس مشخص می‌شود.</p>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-white p-5">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-brand/15 text-brand-dark">
                                    <x-icon name="check" class="size-6" />
                                </span>
                                <h3 class="mt-3 text-base font-extrabold text-navy">دستگاه نو</h3>
                                <p class="mt-2 text-sm leading-7 text-slate-600">گزینه‌های نو با مدل‌ها و ظرفیت‌های مختلف. برای اطلاع از موجودی و شرایط خرید، با ما تماس بگیرید.</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-5">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-navy/10 text-navy">
                                    <x-icon name="refresh" class="size-6" />
                                </span>
                                <h3 class="mt-3 text-base font-extrabold text-navy">دستگاه دست‌دوم</h3>
                                <p class="mt-2 text-sm leading-7 text-slate-600">گزینه‌های کارکرده با شرایط واقعی هر دستگاه. وضعیت فنی و قطعات پیش از خرید بررسی می‌شود.</p>
                            </div>
                        </div>

                        <x-contact-buttons class="mt-5" />
                    </div>

                    @if ($service->faqs->isNotEmpty())
                        <div class="mt-10">
                            <div class="mb-5 flex items-center gap-3">
                                <span class="h-7 w-1 rounded-full bg-brand"></span>
                                <h2 class="text-xl font-extrabold text-navy sm:text-2xl">سوالات متداول این حوزه</h2>
                            </div>

                            <x-faq-list :faqs="$service->faqs" />
                        </div>
                    @endif

                    @if ($others->isNotEmpty())
                        <div class="mt-10">
                            <h2 class="text-xl font-extrabold text-navy">سایر حوزه‌های کاری</h2>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                @foreach ($others as $other)
                                    <a href="{{ route('services.show', $other->slug) }}" class="group flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3.5 transition hover:border-brand/60 hover:bg-cream">
                                        <span class="flex items-center gap-3">
                                            <span class="flex size-9 items-center justify-center rounded-lg bg-cream text-navy transition group-hover:bg-brand group-hover:text-navy-950">
                                                <x-icon name="factory" class="size-4" />
                                            </span>
                                            <span class="text-sm font-bold text-navy">{{ $other->title }}</span>
                                        </span>
                                        <x-icon name="arrow-left" class="size-4 text-slate-400 transition group-hover:-translate-x-1 group-hover:text-brand-dark" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-4">
                    <div class="space-y-4 lg:sticky lg:top-24">
                        <div class="rounded-2xl bg-navy p-6 text-white shadow-lg">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-white/10 text-brand">
                                <x-icon name="headset" class="size-6" />
                            </span>
                            <h2 class="mt-4 text-lg font-extrabold">مشاوره خرید دستگاه</h2>
                            <p class="mt-2 text-sm leading-7 text-white/65">نوع تولید و ظرفیت موردنظر خود را بگویید تا گزینه‌های مناسب را بررسی کنیم.</p>

                            <a href="{{ \App\Support\Site::phoneHref() }}" class="mt-5 flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-3 transition hover:bg-white/10">
                                <span class="flex size-9 items-center justify-center rounded-lg bg-brand text-navy-950">
                                    <x-icon name="phone" class="size-4" />
                                </span>
                                <span class="flex flex-col">
                                    <span class="text-[11px] text-white/50">تماس مستقیم</span>
                                    <span dir="ltr" class="text-base font-black">{{ \App\Support\Site::phoneDisplay() }}</span>
                                </span>
                            </a>

                            <x-contact-buttons call-style="outline" class="mt-4 !flex-col !items-stretch" />

                            <div class="mt-5 space-y-2 border-t border-white/10 pt-4 text-xs text-white/55">
                                <p class="flex items-center gap-2"><x-icon name="clock" class="size-4 text-brand" /> {{ setting('working_hours') }}</p>
                                <p class="flex items-start gap-2"><x-icon name="pin" class="mt-0.5 size-4 shrink-0 text-brand" /> {{ setting('address') }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <h2 class="text-sm font-extrabold text-navy">راهنمای سریع</h2>
                            <ul class="mt-3 space-y-3 text-xs leading-6 text-slate-600">
                                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> نوع محصول و ظرفیت موردنیاز را مشخص کنید.</li>
                                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> فضای کارگاه و توان برق را بررسی کنید.</li>
                                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> برای دستگاه دست‌دوم، وضعیت فنی را جدی بگیرید.</li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="bg-cream py-12 sm:py-16">
        <div class="container-site">
            <div class="relative overflow-hidden rounded-[1.75rem] bg-navy-950 px-5 py-8 text-white sm:px-10 sm:py-10">
                <img src="{{ asset('images/hero-factory.svg') }}" alt="" width="1600" height="950" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-25">
                <div class="absolute inset-0 bg-gradient-to-l from-navy-950 via-navy-950/90 to-navy-950/60"></div>

                <div class="relative flex flex-col items-center justify-between gap-6 text-center lg:flex-row lg:text-start">
                    <div>
                        <h2 class="text-xl font-extrabold leading-8 sm:text-2xl">درباره‌ی {{ $service->title }} سوالی دارید؟</h2>
                        <p class="mt-2 text-sm text-white/60">تماس بگیرید تا مدل‌ها و شرایط موجود را بررسی کنیم.</p>
                    </div>
                    <x-contact-buttons call-style="outline" class="shrink-0 justify-center" />
                </div>
            </div>
        </div>
    </section>

    <x-schema-faq :faqs="$service->faqs" />
@endsection
