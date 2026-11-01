<footer class="relative overflow-hidden bg-navy-950 text-white">
    <div class="absolute inset-0 hero-glow opacity-60"></div>

    <div class="container-site relative pt-12 pb-28 sm:pt-16 md:pb-16">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-4">
                <x-logo variant="light" />

                <p class="mt-5 max-w-sm text-sm leading-7 text-white/65">
                    واسطه‌گری خرید و فروش دستگاه‌های تولید، دوخت، برش و چاپ نایلون در شهرک صنعتی تجریشی. برای انتخاب دستگاه مناسب، با ما تماس بگیرید.
                </p>

                <a href="{{ \App\Support\Site::phoneHref() }}" class="mt-6 inline-flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:border-brand/60 hover:bg-white/10">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-brand text-navy-950">
                        <x-icon name="phone" class="size-5" />
                    </span>
                    <span class="flex flex-col">
                        <span class="text-[11px] text-white/55">تماس مستقیم</span>
                        <span dir="ltr" class="text-lg font-black tracking-wide">{{ \App\Support\Site::phoneDisplay() }}</span>
                    </span>
                </a>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-sm font-extrabold text-brand">دسترسی سریع</h2>
                <ul class="mt-4 space-y-3 text-sm text-white/65">
                    <li><a href="{{ route('home') }}" class="transition hover:text-brand">خانه</a></li>
                    <li><a href="{{ route('home') }}#services" class="transition hover:text-brand">حوزه‌های کاری</a></li>
                    <li><a href="{{ route('home') }}#about" class="transition hover:text-brand">درباره ما</a></li>
                    <li><a href="{{ route('blog.index') }}" class="transition hover:text-brand">مقالات</a></li>
                    <li><a href="{{ route('faq') }}" class="transition hover:text-brand">سوالات متداول</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-brand">تماس با ما</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-extrabold text-brand">اطلاعات تماس</h2>

                <ul class="mt-4 space-y-4 text-sm text-white/65">
                    <li class="flex items-start gap-3">
                        <x-icon name="pin" class="mt-0.5 size-5 shrink-0 text-brand" />
                        <span class="leading-7">{{ setting('address', 'تهران، شهرک صنعتی تجریشی') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="clock" class="mt-0.5 size-5 shrink-0 text-brand" />
                        <span class="leading-7">{{ setting('working_hours', 'شنبه تا پنجشنبه، ۹ صبح تا ۵ عصر') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="whatsapp" class="mt-0.5 size-5 shrink-0 text-brand" />
                        <a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener" class="leading-7 transition hover:text-brand">مشاوره در واتساپ</a>
                    </li>
                </ul>

                @if (setting('socials_enabled', '0') === '1' && (setting('instagram') || setting('telegram')))
                    <div class="mt-5 flex items-center gap-2">
                        @if (setting('instagram'))
                            <a href="{{ setting('instagram') }}" target="_blank" rel="noopener" class="flex size-9 items-center justify-center rounded-lg border border-white/15 text-white/80 transition hover:border-brand hover:text-brand" aria-label="اینستاگرام">
                                <x-icon name="instagram" class="size-4" />
                            </a>
                        @endif
                        @if (setting('telegram'))
                            <a href="{{ setting('telegram') }}" target="_blank" rel="noopener" class="flex size-9 items-center justify-center rounded-lg border border-white/15 text-white/80 transition hover:border-brand hover:text-brand" aria-label="تلگرام">
                                <x-icon name="telegram" class="size-4" />
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-extrabold text-brand">موقعیت ما</h2>
                <x-map class="mt-4 h-40 w-full border border-white/10" />
                <a href="{{ setting('map_url') ?: '#' }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-white/70 transition hover:text-brand">
                    مسیریابی به شهرک صنعتی تجریشی
                    <x-icon name="external" class="size-3.5" />
                </a>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-center text-xs text-white/45 sm:flex-row sm:text-start">
            <p>© {{ \App\Support\Jalali::year(now()) }} {{ setting('site_name', 'پلاستو ماشین') }}؛ تمامی حقوق محفوظ است.</p>
            <p>طراحی و توسعه‌ی اختصاصی</p>
        </div>
    </div>
</footer>
