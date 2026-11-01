@extends('admin.layouts.app')

@section('title', 'داشبورد')

@section('content')
    <x-admin.page-header
        title="داشبورد"
        description="خلاصه‌ی وضعیت محتوا و پیام‌های دریافتی سایت."
    >
        <a href="{{ route('admin.services.create') }}" class="btn-brand !rounded-lg !px-4 !py-2.5">
            <x-icon name="plus" class="size-4" />
            صفحه خدماتی جدید
        </a>
        <a href="{{ route('admin.posts.create') }}" class="btn" data-variant="outline">
            <x-icon name="file" class="size-4" />
            مقاله جدید
        </a>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-brand/15 text-brand-dark">
                    <x-icon name="inbox" class="size-5" />
                </span>
                <span class="text-2xl font-black text-navy">{{ fa_num($newMessages) }}</span>
            </div>
            <p class="mt-4 text-sm font-bold text-navy">پیام‌های جدید</p>
            <p class="mt-1 text-xs text-slate-500">در انتظار بررسی و پیگیری</p>
            <a href="{{ route('admin.messages.index', ['status' => 'new']) }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-dark hover:underline">
                مشاهده‌ی پیام‌ها
                <x-icon name="arrow-left" class="size-3.5" />
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-navy/10 text-navy">
                    <x-icon name="message" class="size-5" />
                </span>
                <span class="text-2xl font-black text-navy">{{ fa_num($totalMessages) }}</span>
            </div>
            <p class="mt-4 text-sm font-bold text-navy">کل پیام‌ها</p>
            <p class="mt-1 text-xs text-slate-500">از ابتدا تا امروز</p>
            <a href="{{ route('admin.messages.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-dark hover:underline">
                آرشیو پیام‌ها
                <x-icon name="arrow-left" class="size-3.5" />
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <x-icon name="factory" class="size-5" />
                </span>
                <span class="text-2xl font-black text-navy">{{ fa_num($servicesCount) }}</span>
            </div>
            <p class="mt-4 text-sm font-bold text-navy">صفحات خدماتی</p>
            <p class="mt-1 text-xs text-slate-500">فعال و غیرفعال</p>
            <a href="{{ route('admin.services.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-dark hover:underline">
                مدیریت صفحات
                <x-icon name="arrow-left" class="size-3.5" />
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <x-icon name="file" class="size-5" />
                </span>
                <span class="text-2xl font-black text-navy">{{ fa_num($postsCount) }}</span>
            </div>
            <p class="mt-4 text-sm font-bold text-navy">مقالات</p>
            <p class="mt-1 text-xs text-slate-500">پیش‌نویس و منتشرشده</p>
            <a href="{{ route('admin.posts.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-dark hover:underline">
                مدیریت مقالات
                <x-icon name="arrow-left" class="size-3.5" />
            </a>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-extrabold text-navy">آخرین پیام‌ها</h2>
                    <p class="mt-0.5 text-[11px] text-slate-400">شش پیام اخیر فرم تماس</p>
                </div>
                <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold text-brand-dark hover:underline">همه</a>
            </div>

            @if ($latestMessages->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <x-icon name="inbox" class="size-6" />
                    </span>
                    <p class="mt-3 text-sm text-slate-500">هنوز پیامی دریافت نشده است.</p>
                </div>
            @else
                <div class="table-container">
                    <table class="table">
                        <thead class="bg-slate-50">
                            <tr>
                                <th>نام</th>
                                <th>شماره تماس</th>
                                <th>تاریخ</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestMessages as $message)
                                <tr>
                                    <td class="font-bold text-navy">{{ $message->name }}</td>
                                    <td dir="ltr" class="text-start text-slate-500">{{ \App\Support\Site::phoneDisplay($message->phone) }}</td>
                                    <td class="text-xs text-slate-400">{{ jalali_date($message->created_at, true) }}</td>
                                    <td>
                                        <span class="badge border {{ $message->status === 'new' ? 'border-brand/30 bg-brand/15 text-brand-dark' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                            {{ $message->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn" data-variant="ghost" data-size="sm">مشاهده</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-navy">دسترسی سریع</h2>
            <p class="mt-1 text-[11px] text-slate-400">کارهای رایج روزانه</p>

            <div class="mt-4 grid gap-2">
                <a href="{{ route('admin.messages.index', ['status' => 'new']) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-3 transition hover:border-brand/50 hover:bg-cream">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-brand/15 text-brand-dark"><x-icon name="inbox" class="size-4" /></span>
                    <span class="flex-1 text-sm font-bold text-navy">بررسی پیام‌های جدید</span>
                    <span class="badge" data-variant="secondary">{{ fa_num($newMessages) }}</span>
                </a>

                <a href="{{ route('admin.services.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-3 transition hover:border-brand/50 hover:bg-cream">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="plus" class="size-4" /></span>
                    <span class="flex-1 text-sm font-bold text-navy">افزودن صفحه خدماتی</span>
                    <x-icon name="arrow-left" class="size-4 text-slate-400" />
                </a>

                <a href="{{ route('admin.faqs.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-3 transition hover:border-brand/50 hover:bg-cream">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="message" class="size-4" /></span>
                    <span class="flex-1 text-sm font-bold text-navy">افزودن سوال متداول</span>
                    <x-icon name="arrow-left" class="size-4 text-slate-400" />
                </a>

                <a href="{{ route('admin.settings.edit') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-3 transition hover:border-brand/50 hover:bg-cream">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="settings" class="size-4" /></span>
                    <span class="flex-1 text-sm font-bold text-navy">ویرایش تنظیمات سایت</span>
                    <x-icon name="arrow-left" class="size-4 text-slate-400" />
                </a>
            </div>

            <div class="mt-5 rounded-xl bg-cream p-4 text-xs leading-6 text-slate-500">
                <p class="flex items-center gap-1.5 font-bold text-navy">
                    <x-icon name="shield" class="size-4 text-brand-dark" />
                    نکته‌ی امنیتی
                </p>
                <p class="mt-1">پس از پایان کار، از حساب مدیر خارج شوید. صفحه‌ی ثبت‌نام عمومی وجود ندارد.</p>
            </div>
        </div>
    </div>
@endsection
