@extends('layouts.site')

@section('content')
    <!-- Hero -->
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <img src="{{ asset('images/hero.jpg') }}" alt="خط تولید نایلون و رول‌های فیلم پلاستیکی" width="1600" height="950"
            class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">

        <!-- دسکتاپ: گرادیانت افقی از سمت متن (راست در RTL) -->
        <div class="absolute inset-0 hidden md:block bg-gradient-to-l from-navy-950 via-navy-950/85 to-navy-950/10"></div>

        <!-- موبایل: گرادیانت عمودی پررنگ -->
        <div class="absolute inset-0 md:hidden bg-gradient-to-t from-navy-950 via-navy-950/85 to-navy-950/60"></div>
        <div class="absolute inset-0 hero-glow"></div>

        <div class="container-site relative flex min-h-[560px] items-center py-14 sm:py-20 lg:min-h-[630px]">
            <div class="grid w-full items-center lg:grid-cols-12">
                <div class="lg:col-span-8 xl:col-span-7">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-bold text-white/80 backdrop-blur">
                        <span class="size-1.5 rounded-full bg-brand"></span>
                        مشاوره خرید و فروش در شهرک صنعتی تجریشی
                    </span>

                    <h1 class="mt-5 max-w-3xl text-[1.7rem] font-black leading-[1.55] text-balance sm:text-4xl lg:text-[2.7rem] lg:leading-[1.5]">
                        خرید و فروش دستگاه تولید و دوخت نایلون
                        <span class="relative inline-block text-brand">
                            در شهرک تجریشی
                            <span class="absolute inset-x-0 -bottom-1 h-1 rounded-full bg-brand/60"></span>
                        </span>
                    </h1>

                    <p class="mt-6 max-w-xl text-sm leading-8 text-white/75 sm:text-base">
                        مشاوره و ارتباط مستقیم برای خرید و فروش دستگاه‌های نو و دست‌دوم؛ از تولید فیلم و کیسه تا دوخت، برش و چاپ نایلون.
                    </p>

                    <x-contact-buttons call-style="outline" class="mt-8" />

                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-xs text-white/60">
                        <span class="flex items-center gap-2">
                            <x-icon name="check" class="size-4 text-brand" />
                            دستگاه نو و دست‌دوم
                        </span>
                        <span class="flex items-center gap-2">
                            <x-icon name="check" class="size-4 text-brand" />
                            مشاوره پیش از خرید
                        </span>
                        <span class="flex items-center gap-2">
                            <x-icon name="check" class="size-4 text-brand" />
                            ارتباط با یک شماره‌ی واحد
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 leading-none">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="h-8 w-full sm:h-12" aria-hidden="true">
                <path d="M0 60C240 8 480 0 720 0s480 8 720 60Z" fill="#ffffff" />
            </svg>
        </div>
    </section>

    <!-- Why / trust -->
    <section class="bg-white py-14 sm:py-20">
        <div class="container-site">
            <x-section-heading kicker="چرا پلاستو ماشین؟" title="خرید دستگاه، با اطلاعات کامل و بدون سردرگمی">
                برای انتخاب دستگاه لازم نیست چند جا را جدا بررسی کنید؛ نیاز شما را می‌شنویم و گزینه‌های مناسب را شفاف معرفی می‌کنیم.
            </x-section-heading>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-cream text-navy">
                        <x-icon name="shield" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-base font-extrabold text-navy">مشاوره تخصصی و راهنمایی کامل</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">پیش از خرید، نیاز شما را بررسی می‌کنیم و اطلاعات فنی دستگاه را شفاف توضیح می‌دهیم.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-cream text-navy">
                        <x-icon name="handshake" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-base font-extrabold text-navy">ارتباط مستقیم، بدون واسطه</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">همه‌ی هماهنگی‌ها با یک شماره‌ی واحد انجام می‌شود؛ از پرسش اولیه تا هماهنگی بازدید.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-cream text-navy">
                        <x-icon name="factory" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-base font-extrabold text-navy">دستگاه‌های نو و دست‌دوم</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">گزینه‌های نو و کارکرده را با شرایط واقعی هر دستگاه معرفی می‌کنیم.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-cream text-navy">
                        <x-icon name="grid" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-base font-extrabold text-navy">تمرکز بر صنعت پلاستیک</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">کار ما مشخص است: ماشین‌آلات تولید، دوخت، برش و چاپ نایلون.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="scroll-mt-24 bg-cream py-14 sm:py-20">
        <div class="container-site">
            <x-section-heading kicker="حوزه‌های کاری" title="دستگاه‌هایی که روی آن‌ها تمرکز داریم">
                روی هر حوزه بزنید تا توضیحات، نکات خرید و سوالات متداول آن را ببینید.
            </x-section-heading>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:mt-12 lg:grid-cols-3 lg:gap-6">
                @foreach ($services as $service)
                    <x-service-card :service="$service" :index="$loop->iteration" />
                @endforeach

                <a href="{{ route('contact') }}" class="group relative flex h-full min-h-[19rem] flex-col justify-between overflow-hidden rounded-[1.75rem] bg-navy p-6 text-white shadow-sm transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_28px_60px_-28px_rgba(11,41,73,0.6)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:p-7">
                    <img
                        src="{{ asset('images/hero.png') }}"
                        alt=""
                        width="1600"
                        height="950"
                        loading="lazy"
                        class="absolute inset-0 h-full w-full object-cover opacity-30 transition duration-700 group-hover:scale-105 group-hover:opacity-40"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/85 to-navy-950/35"></div>

                    <div class="relative flex items-center justify-between gap-3">
                        <span class="flex size-14 items-center justify-center rounded-2xl bg-white/10 text-brand ring-1 ring-inset ring-white/15 backdrop-blur">
                            <x-icon name="headset" class="size-7" />
                        </span>
                        <span class="flex size-10 items-center justify-center rounded-full border border-white/20 bg-white/5 text-white/70 transition duration-300 group-hover:border-brand group-hover:bg-brand group-hover:text-navy-950">
                            <x-icon name="arrow-left" class="size-4" />
                        </span>
                    </div>

                    <div class="relative mt-8">
                        <h3 class="text-lg font-extrabold leading-8">حوزه‌ی دیگری مدنظر دارید؟</h3>
                        <p class="mt-2 text-sm leading-7 text-white/65">نیاز خود را با ما در میان بگذارید تا گزینه‌های مرتبط را بررسی کنیم.</p>
                    </div>

                    <span class="relative mt-5 inline-flex items-center gap-1.5 text-sm font-bold text-brand">
                        گفتگو با ما
                        <x-icon name="arrow-left" class="size-4 transition group-hover:-translate-x-1" />
                    </span>
                </a>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="scroll-mt-24 overflow-hidden bg-white py-14 sm:py-20">
        <div class="container-site">
            <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-6">
                    <span class="section-kicker !justify-start">
                        <span class="h-px w-6 bg-brand"></span>
                        درباره ما
                    </span>

                    <h2 class="text-2xl font-extrabold leading-[1.6] text-navy sm:text-3xl">واسطه‌ی خرید و فروش دستگاه‌های نایلون در تجریشی</h2>

                    <div class="mt-5 space-y-4 text-sm leading-8 text-slate-600 sm:text-[15px]">
                        <p>
                            پلاستو ماشین با هدف تسهیل خرید و فروش ماشین‌آلات تولید و دوخت نایلون فعالیت می‌کند. اگر به دستگاه تولید کیسه زباله، خط تولید فیلم، دوخت و برش یا چاپ نایلون نیاز دارید، می‌توانید نیاز خود را با ما در میان بگذارید.
                        </p>
                        <p>
                            ما دستگاه‌های نو و دست‌دوم را با توجه به نوع محصول، ظرفیت و فضای کارگاه شما معرفی می‌کنیم. نوع، مدل و قیمت دستگاه با تماس مشخص می‌شود؛ زیرا هر دستگاه شرایط فنی و تجهیزات متفاوتی دارد.
                        </p>
                    </div>

                    <ul class="mt-6 grid gap-3 text-sm text-navy sm:grid-cols-2">
                        <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-brand-dark" /> معرفی دستگاه مناسب نیاز شما</li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-brand-dark" /> بررسی گزینه‌های نو و دست‌دوم</li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-brand-dark" /> هماهنگی بازدید و تماس مستقیم</li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-brand-dark" /> مشاوره پیش از تصمیم‌گیری</li>
                    </ul>

                    <x-contact-buttons class="mt-8" />
                </div>

                <div class="relative lg:col-span-6">
                    <div class="absolute -bottom-4 -start-4 h-40 w-40 rounded-2xl border-[10px] border-brand/20"></div>
                    <div class="relative overflow-hidden rounded-[1.75rem] border-4 border-white shadow-2xl">
                        <img src="{{ asset('images/about.jpg') }}" alt="رول‌های نایلون تولیدشده در کارخانه" width="1200" height="900" loading="lazy" class="aspect-[4/3] h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/35 to-transparent"></div>
                    </div>
                    <div class="absolute bottom-4 start-4 flex items-center gap-2 rounded-xl bg-white/95 px-3 py-2 text-xs font-bold text-navy shadow-lg backdrop-blur">
                        <x-icon name="pin" class="size-4 text-brand-dark" />
                        شهرک صنعتی تجریشی
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA banner -->
    <section class="bg-cream pb-14 pt-2 sm:pb-20">
        <div class="container-site">
            <div class="relative overflow-hidden rounded-[1.75rem] bg-navy-950 px-5 py-8 text-white shadow-xl sm:px-10 sm:py-10">
                <img src="{{ asset('images/hero-factory.svg') }}" alt="" width="1600" height="950" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-25">
                <div class="absolute inset-0 bg-gradient-to-l from-navy-950 via-navy-950/90 to-navy-950/60"></div>

                <div class="relative flex flex-col items-center justify-between gap-6 text-center lg:flex-row lg:text-start">
                    <div class="flex flex-col items-center gap-4 sm:flex-row lg:items-start">
                        <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-brand">
                            <x-icon name="headset" class="size-7" />
                        </span>
                        <div>
                            <h2 class="text-xl font-extrabold leading-8 text-balance sm:text-2xl">
                                برای دریافت مشاوره و اطلاعات بیشتر
                                <span class="text-brand">همین حالا با ما در تماس باشید.</span>
                            </h2>
                            <p class="mt-2 text-xs leading-6 text-white/60 sm:text-sm">نوع دستگاه و نیاز خود را بگویید؛ راهنمایی لازم را دریافت می‌کنید.</p>
                        </div>
                    </div>

                    <x-contact-buttons call-style="outline" class="shrink-0 justify-center" />
                </div>
            </div>
        </div>
    </section>

    <x-schema-local-business />
@endsection
