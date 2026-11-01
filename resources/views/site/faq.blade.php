@extends('layouts.site')

@section('content')
    <x-page-hero
        title="سوالات متداول"
        breadcrumb="سوالات متداول"
        subtitle="پاسخ پرسش‌های رایج درباره‌ی انتخاب، خرید و هماهنگی دستگاه‌های تولید و دوخت نایلون."
    >
        <x-contact-buttons call-style="outline" />
    </x-page-hero>

    <section class="bg-cream py-12 sm:py-16">
        <div class="container-site">
            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    @if ($faqs->isEmpty())
                        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">هنوز سوالی ثبت نشده است.</div>
                    @else
                        <x-faq-list :faqs="$faqs" />
                    @endif
                </div>

                <aside class="lg:col-span-4">
                    <div class="space-y-4 lg:sticky lg:top-24">
                        <div class="rounded-2xl bg-navy p-6 text-white shadow-lg">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-white/10 text-brand">
                                <x-icon name="headset" class="size-6" />
                            </span>
                            <h2 class="mt-4 text-lg font-extrabold">پاسخ سوال خود را پیدا نکردید؟</h2>
                            <p class="mt-2 text-sm leading-7 text-white/60">تماس بگیرید یا در واتساپ پیام بدهید؛ درباره‌ی دستگاه موردنظرتان راهنمایی می‌کنیم.</p>

                            <a href="{{ \App\Support\Site::phoneHref() }}" dir="ltr" class="mt-4 block text-xl font-black text-brand">{{ \App\Support\Site::phoneDisplay() }}</a>
                            <x-contact-buttons call-style="outline" class="mt-4 !flex-col !items-stretch" />
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <h2 class="text-sm font-extrabold text-navy">دسترسی سریع</h2>
                            <ul class="mt-3 space-y-2 text-sm">
                                <li><a href="{{ route('home') }}#services" class="text-slate-600 transition hover:text-brand-dark">حوزه‌های کاری</a></li>
                                <li><a href="{{ route('blog.index') }}" class="text-slate-600 transition hover:text-brand-dark">مقالات و راهنماها</a></li>
                                <li><a href="{{ route('contact') }}" class="text-slate-600 transition hover:text-brand-dark">تماس با ما</a></li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <x-schema-faq :faqs="$faqs" />
@endsection
