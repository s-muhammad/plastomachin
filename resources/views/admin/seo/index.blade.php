@extends('admin.layouts.app')

@section('title', 'سئوی صفحات')

@section('content')
    <x-admin.page-header
        title="سئوی صفحات ثابت"
        description="عنوان، توضیح، canonical و تصویر شبکه‌های اجتماعی هر صفحه‌ی ثابت را تنظیم کنید. صفحات خدماتی و مقالات تنظیمات سئوی خود را دارند."
    />

    @php
        $pageUrls = [
            'home' => route('home'),
            'contact' => route('contact'),
            'blog' => route('blog.index'),
            'faq' => route('faq'),
        ];
    @endphp

    <div class="space-y-4">
        @foreach ($pages as $page)
            <details class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none items-center gap-3 px-5 py-4 transition hover:bg-slate-50">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <x-icon name="tag" class="size-4" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-extrabold text-navy">{{ $page->label }}</h2>
                        <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ $page->title ?: 'بدون عنوان اختصاصی' }}</p>
                    </div>

                    @if (isset($pageUrls[$page->key]))
                        <a href="{{ $pageUrls[$page->key] }}" target="_blank" rel="noopener" class="btn hidden sm:inline-flex" data-variant="ghost" data-size="sm">
                            <x-icon name="external" class="size-3.5" />
                            مشاهده
                        </a>
                    @endif

                    <x-icon name="chevron-down" class="size-4 shrink-0 text-slate-400 transition group-open:rotate-180" />
                </summary>

                <form method="POST" action="{{ route('admin.seo.update', $page) }}" enctype="multipart/form-data" class="border-t border-slate-100 p-5">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 lg:grid-cols-2">
                        <x-admin.field
                            name="title"
                            label="عنوان متا"
                            :value="$page->title"
                            maxlength="255"
                            help="در تب مرورگر و نتایج جستجو نمایش داده می‌شود."
                        />

                        <x-admin.field
                            name="og_title"
                            label="عنوان Open Graph"
                            :value="$page->og_title"
                            maxlength="255"
                            help="در صورت خالی بودن، از عنوان متا استفاده می‌شود."
                        />

                        <div class="lg:col-span-2">
                            <x-admin.textarea
                                name="description"
                                label="توضیح متا"
                                :value="$page->description"
                                :rows="3"
                                maxlength="500"
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <x-admin.textarea
                                name="og_description"
                                label="توضیح Open Graph"
                                :value="$page->og_description"
                                :rows="2"
                                maxlength="500"
                                help="در صورت خالی بودن، از توضیح متا استفاده می‌شود."
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <x-admin.field
                                name="canonical"
                                label="آدرس Canonical"
                                :value="$page->canonical"
                                dir="ltr"
                                maxlength="255"
                                placeholder="{{ $pageUrls[$page->key] ?? 'https://plastomachin.ir/' }}"
                                help="اگر خالی باشد، آدرس فعلی صفحه استفاده می‌شود."
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center">
                                @if ($page->og_image)
                                    <img src="{{ \App\Models\ServicePage::imageUrl($page->og_image) }}" alt="{{ $page->label }}" width="240" height="150" class="h-20 w-32 shrink-0 rounded-lg border border-slate-200 object-cover">
                                @else
                                    <div class="flex h-20 w-32 shrink-0 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-white text-slate-400">
                                        <x-icon name="image" class="size-6" />
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <x-admin.field
                                        name="og_image"
                                        label="تصویر شبکه‌های اجتماعی"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        help="ابعاد پیشنهادی ۱۲۰۰×۶۳۰ پیکسل، حداکثر ۴ مگابایت."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
                        <button type="submit" class="btn-brand !rounded-lg !py-2.5">
                            <x-icon name="save" class="size-4" />
                            ذخیره‌ی سئو
                        </button>
                        @if (isset($pageUrls[$page->key]))
                            <a href="{{ $pageUrls[$page->key] }}" target="_blank" rel="noopener" class="btn sm:hidden" data-variant="ghost">مشاهده‌ی صفحه</a>
                        @endif
                    </div>
                </form>
            </details>
        @endforeach
    </div>
@endsection
