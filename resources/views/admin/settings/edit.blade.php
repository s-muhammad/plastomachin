@extends('admin.layouts.app')

@section('title', 'تنظیمات سایت')

@section('content')
    <x-admin.page-header
        title="تنظیمات سایت"
        description="اطلاعات تماس، شبکه‌های اجتماعی و متاهای پیش‌فرض. پس از ذخیره، کش تنظیمات به‌صورت خودکار پاک می‌شود."
    >
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn" data-variant="outline">
            <x-icon name="external" class="size-4" />
            مشاهده سایت
        </a>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="grid gap-5 lg:grid-cols-12">
            <div class="space-y-5 lg:col-span-8">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-sm font-extrabold text-navy">اطلاعات عمومی</h2>
                    <p class="mt-1 text-[11px] text-slate-400">نام و شعار سایت در هدر، فوتر و عنوان صفحات استفاده می‌شود.</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-admin.field name="site_name" label="نام سایت" :value="$values['site_name'] ?? ''" required maxlength="100" />
                        <x-admin.field name="tagline" label="شعار سایت" :value="$values['tagline'] ?? ''" maxlength="200" />
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-sm font-extrabold text-navy">اطلاعات تماس</h2>
                    <p class="mt-1 text-[11px] text-slate-400">شماره‌ها در هدر، فوتر، دکمه‌های تماس و نوار پایین موبایل نمایش داده می‌شوند.</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-admin.field
                            name="phone"
                            label="شماره تلفن"
                            :value="$values['phone'] ?? ''"
                            dir="ltr"
                            inputmode="tel"
                            placeholder="09121234567"
                            required
                            help="با ارقام انگلیسی و بدون فاصله وارد کنید."
                        />

                        <x-admin.field
                            name="whatsapp"
                            label="شماره واتساپ"
                            :value="$values['whatsapp'] ?? ''"
                            dir="ltr"
                            inputmode="tel"
                            placeholder="989121234567"
                            required
                            help="با پیش‌شماره‌ی کشور؛ مثلاً 989121234567."
                        />

                        <x-admin.field
                            name="working_hours"
                            label="ساعت کاری"
                            :value="$values['working_hours'] ?? ''"
                            maxlength="200"
                        />

                        <x-admin.field
                            name="map_url"
                            label="لینک مسیریابی"
                            :value="$values['map_url'] ?? ''"
                            dir="ltr"
                            maxlength="1000"
                            help="نقشه به‌صورت تصویر محلی نمایش داده می‌شود و این لینک فقط برای مسیریابی است."
                        />

                        <div class="sm:col-span-2">
                            <x-admin.textarea
                                name="address"
                                label="آدرس"
                                :value="$values['address'] ?? ''"
                                :rows="2"
                                required
                                maxlength="300"
                            />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-sm font-extrabold text-navy">متاهای پیش‌فرض</h2>
                    <p class="mt-1 text-[11px] text-slate-400">اگر برای صفحه‌ای عنوان یا توضیح جداگانه ثبت نشده باشد، از این مقادیر استفاده می‌شود.</p>

                    <div class="mt-4 space-y-4">
                        <x-admin.field
                            name="meta_title"
                            label="عنوان متای پیش‌فرض"
                            :value="$values['meta_title'] ?? ''"
                            required
                            maxlength="255"
                        />

                        <x-admin.textarea
                            name="meta_description"
                            label="توضیح متای پیش‌فرض"
                            :value="$values['meta_description'] ?? ''"
                            :rows="4"
                            maxlength="500"
                        />
                    </div>
                </div>
            </div>

            <div class="space-y-5 lg:col-span-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-extrabold text-navy">شبکه‌های اجتماعی</h2>
                    <p class="mt-1 text-[11px] leading-6 text-slate-400">آیکون‌ها فقط در صورت فعال بودن این گزینه در فوتر نمایش داده می‌شوند.</p>

                    <div class="mt-4 space-y-4">
                        <x-admin.checkbox
                            name="socials_enabled"
                            label="نمایش آیکون‌های شبکه‌های اجتماعی"
                            :checked="($values['socials_enabled'] ?? '0') === '1'"
                        />

                        <x-admin.field
                            name="instagram"
                            label="لینک اینستاگرام"
                            :value="$values['instagram'] ?? ''"
                            dir="ltr"
                            maxlength="255"
                            placeholder="https://instagram.com/..."
                        />

                        <x-admin.field
                            name="telegram"
                            label="لینک تلگرام"
                            :value="$values['telegram'] ?? ''"
                            dir="ltr"
                            maxlength="255"
                            placeholder="https://t.me/..."
                        />
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <button type="submit" class="btn-brand !w-full !rounded-lg !py-2.5">
                        <x-icon name="save" class="size-4" />
                        ذخیره‌ی تنظیمات
                    </button>

                    <p class="mt-3 text-center text-[11px] leading-6 text-slate-400">
                        تغییرات بلافاصله در سایت اعمال می‌شود.
                    </p>
                </div>

                <div class="rounded-2xl border border-brand/30 bg-brand/10 p-5 text-xs leading-6 text-slate-600">
                    <p class="flex items-center gap-1.5 font-bold text-navy">
                        <x-icon name="shield" class="size-4 text-brand-dark" />
                        بدون وابستگی خارجی
                    </p>
                    <p class="mt-1">فونت، آیکون‌ها و فایل‌های استایل به‌صورت محلی بارگذاری می‌شوند.</p>
                </div>
            </div>
        </div>
    </form>
@endsection
