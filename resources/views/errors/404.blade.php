@extends('layouts.site')

@section('content')
    <section class="bg-cream py-16 sm:py-24">
        <div class="container-site">
            <div class="mx-auto max-w-xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">
                <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-cream text-navy">
                    <x-icon name="factory" class="size-9" />
                </span>

                <p class="mt-6 text-sm font-bold text-brand-dark">خطای ۴۰۴</p>
                <h1 class="mt-2 text-2xl font-black text-navy sm:text-3xl">این صفحه پیدا نشد</h1>
                <p class="mt-3 text-sm leading-7 text-slate-600">ممکن است آدرس تغییر کرده باشد یا صفحه حذف شده باشد. از مسیرهای زیر ادامه دهید.</p>

                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('home') }}" class="btn-navy">بازگشت به خانه</a>
                    <a href="{{ route('contact') }}" class="btn-brand">تماس با ما</a>
                </div>

                <div class="mt-6 flex flex-wrap justify-center gap-4 text-xs font-bold text-slate-500">
                    <a href="{{ route('blog.index') }}" class="transition hover:text-brand-dark">مقالات</a>
                    <a href="{{ route('faq') }}" class="transition hover:text-brand-dark">سوالات متداول</a>
                    <a href="{{ route('home') }}#services" class="transition hover:text-brand-dark">حوزه‌های کاری</a>
                </div>
            </div>
        </div>
    </section>
@endsection
