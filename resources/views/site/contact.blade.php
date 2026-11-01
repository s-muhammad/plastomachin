@extends('layouts.site')

@section('content')
    <x-page-hero
        title="تماس با ما"
        breadcrumb="تماس با ما"
        subtitle="برای مشاوره خرید و فروش دستگاه تولید و دوخت نایلون، سوال درباره‌ی مدل‌ها یا هماهنگی بازدید با ما در تماس باشید."
    >
        <x-contact-buttons call-style="outline" />
    </x-page-hero>

    <section class="bg-cream py-12 sm:py-16">
        <div class="container-site">
            <div class="grid gap-6 lg:grid-cols-12">
                <!-- Form -->
                <div class="lg:col-span-7">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                        <div class="flex items-start gap-3">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand/15 text-brand-dark">
                                <x-icon name="message" class="size-6" />
                            </span>
                            <div>
                                <h2 class="text-xl font-extrabold text-navy">پیام خود را بفرستید</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">نام و شماره تماس را وارد کنید تا با شما تماس بگیریم. پیام اختیاری است.</p>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert mt-6" role="status">
                                <x-icon name="check" class="size-5 text-whatsapp" />
                                <h3>پیام شما ثبت شد</h3>
                                <section>{{ session('success') }}</section>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert mt-6" data-variant="destructive" role="alert">
                                <x-icon name="shield" class="size-5" />
                                <h3>لطفاً موارد مشخص‌شده را بررسی کنید</h3>
                                <section>اطلاعات فرم کامل یا معتبر نیست.</section>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="mt-6 space-y-5">
                            @csrf
                            <input type="hidden" name="source_page" value="{{ request('source', url()->previous()) }}">

                            {{-- Honeypot: hidden from real users --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="website">وب‌سایت</label>
                                <input id="website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="field" @if ($errors->has('name')) data-invalid @endif>
                                    <label for="name">نام و نام خانوادگی <span class="text-destructive">*</span></label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="مثلاً محمد رضایی" @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif required>
                                    @error('name') <p id="name-error" role="alert">{{ $message }}</p> @enderror
                                </div>

                                <div class="field" @if ($errors->has('phone')) data-invalid @endif>
                                    <label for="phone">شماره تماس <span class="text-destructive">*</span></label>
                                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="۰۹۱۲۱۲۳۴۵۶۷" dir="ltr" class="text-start" @if ($errors->has('phone')) aria-invalid="true" aria-describedby="phone-error" @endif required>
                                    @error('phone') <p id="phone-error" role="alert">{{ $message }}</p> @else <p>شماره موبایل یا تلفن ثابت</p> @enderror
                                </div>
                            </div>

                            <div class="field" @if ($errors->has('message')) data-invalid @endif>
                                <label for="message">پیام (اختیاری)</label>
                                <textarea id="message" name="message" rows="5" placeholder="درباره‌ی دستگاه موردنظر، ظرفیت یا بودجه‌ی تقریبی بنویسید." @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message') }}</textarea>
                                @error('message') <p id="message-error" role="alert">{{ $message }}</p> @else <p>هرچه اطلاعات بیشتری بدهید، راهنمایی دقیق‌تری دریافت می‌کنید.</p> @enderror
                            </div>

                            <p class="text-xs leading-6 text-slate-500">
                                با ارسال فرم، فقط برای پیگیری همین درخواست با شما تماس می‌گیریم. اطلاعات شما در اختیار شخص ثالث قرار نمی‌گیرد.
                            </p>

                            <button type="submit" class="btn-brand w-full sm:w-auto">
                                <x-icon name="send" class="size-4" />
                                ارسال پیام
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact info -->
                <aside class="space-y-4 lg:col-span-5">
                    <div class="rounded-2xl bg-navy p-6 text-white shadow-lg">
                        <h2 class="text-lg font-extrabold">راه‌های ارتباطی</h2>
                        <p class="mt-2 text-sm leading-7 text-white/60">برای سریع‌ترین پاسخ، تماس تلفنی یا واتساپ را انتخاب کنید.</p>

                        <div class="mt-5 space-y-3">
                            <a href="{{ \App\Support\Site::phoneHref() }}" class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 transition hover:bg-white/10">
                                <span class="flex size-10 items-center justify-center rounded-lg bg-brand text-navy-950"><x-icon name="phone" class="size-5" /></span>
                                <span class="flex flex-col">
                                    <span class="text-[11px] text-white/50">تلفن</span>
                                    <span dir="ltr" class="text-base font-black">{{ \App\Support\Site::phoneDisplay() }}</span>
                                </span>
                            </a>

                            <a href="{{ \App\Support\Site::whatsappUrl(null, 'سلام، برای خرید دستگاه نایلون راهنمایی می‌خواهم.') }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 transition hover:bg-white/10">
                                <span class="flex size-10 items-center justify-center rounded-lg bg-whatsapp text-white"><x-icon name="whatsapp" class="size-5" /></span>
                                <span class="flex flex-col">
                                    <span class="text-[11px] text-white/50">واتساپ</span>
                                    <span class="text-sm font-bold">مشاوره و ارسال پیام</span>
                                </span>
                            </a>
                        </div>

                        <div class="mt-5 space-y-3 border-t border-white/10 pt-4 text-sm text-white/65">
                            <p class="flex items-start gap-2"><x-icon name="pin" class="mt-0.5 size-4 shrink-0 text-brand" /> {{ setting('address') }}</p>
                            <p class="flex items-start gap-2"><x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-brand" /> {{ setting('working_hours') }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <x-map class="h-56 w-full" />
                        <a href="{{ setting('map_url') ?: '#' }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-navy transition hover:text-brand-dark">
                            مشاهده مسیر روی نقشه
                            <x-icon name="external" class="size-3.5" />
                        </a>
                    </div>

                    <div class="rounded-2xl border border-brand/30 bg-brand/10 p-5">
                        <h2 class="text-sm font-extrabold text-navy">پیش از تماس، این موارد را آماده کنید</h2>
                        <ul class="mt-3 space-y-2 text-xs leading-6 text-slate-700">
                            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> نوع محصول و ظرفیت تقریبی</li>
                            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> فضای کارگاه و توان برق</li>
                            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-dark" /> دستگاه نو یا دست‌دوم</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <x-schema-local-business />
@endsection
