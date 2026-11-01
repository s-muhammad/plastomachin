@extends('admin.layouts.app')

@section('title', 'جزئیات پیام')

@section('content')
    <x-admin.page-header
        title="جزئیات پیام"
        :description="'دریافت‌شده در '.jalali_date($message->created_at, true)"
    >
        <a href="{{ route('admin.messages.index') }}" class="btn" data-variant="outline">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت به پیام‌ها
        </a>
    </x-admin.page-header>

    <div class="grid gap-5 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-full bg-navy text-base font-black text-white">
                            {{ mb_substr($message->name, 0, 1) }}
                        </span>
                        <div>
                            <h2 class="text-base font-extrabold text-navy">{{ $message->name }}</h2>
                            <p dir="ltr" class="mt-0.5 text-start text-xs text-slate-500">{{ \App\Support\Site::phoneDisplay($message->phone) }}</p>
                        </div>
                    </div>

                    @if ($message->status === 'new')
                        <span class="badge border border-brand/30 bg-brand/15 text-brand-dark">جدید</span>
                    @else
                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">پیگیری‌شده</span>
                    @endif
                </div>

                <div class="p-5">
                    <h3 class="text-[11px] font-bold text-slate-400">متن پیام</h3>
                    @if ($message->message)
                        <div class="mt-2 rounded-xl bg-slate-50 p-4 text-sm leading-8 text-slate-700">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    @else
                        <p class="mt-2 rounded-xl bg-slate-50 p-4 text-sm text-slate-400">مشتری متنی وارد نکرده است. برای اطلاع از نیاز او تماس بگیرید.</p>
                    @endif

                    <dl class="mt-5 grid gap-4 text-xs sm:grid-cols-2">
                        <div>
                            <dt class="font-bold text-slate-400">شماره تماس</dt>
                            <dd class="mt-1">
                                <a href="{{ \App\Support\Site::phoneHref($message->phone) }}" dir="ltr" class="font-bold text-navy hover:text-brand-dark">{{ \App\Support\Site::phoneDisplay($message->phone) }}</a>
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-400">تاریخ دریافت</dt>
                            <dd class="mt-1 text-slate-600">{{ jalali_date($message->created_at, true) }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="font-bold text-slate-400">صفحه‌ی مبدأ</dt>
                            <dd class="mt-1 break-all text-slate-600" dir="ltr">{{ $message->source_page ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="space-y-5 lg:col-span-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">اقدام سریع</h2>
                <p class="mt-1 text-[11px] text-slate-400">با یک کلیک تماس بگیرید یا در واتساپ پیام بدهید.</p>

                <div class="mt-4 grid gap-2">
                    <a href="{{ \App\Support\Site::phoneHref($message->phone) }}" class="btn-brand !rounded-lg !py-2.5">
                        <x-icon name="phone" class="size-4" />
                        تماس تلفنی
                    </a>

                    <a href="{{ \App\Support\Site::whatsappUrl($message->phone, 'سلام، درباره‌ی پیام شما در سایت پلاستو ماشین تماس می‌گیریم.') }}" target="_blank" rel="noopener" class="btn-whatsapp !rounded-lg !py-2.5">
                        <x-icon name="whatsapp" class="size-4" />
                        ارسال پیام در واتساپ
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">وضعیت پیگیری</h2>

                <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <label for="status">وضعیت</label>
                        <select id="status" name="status" class="w-full">
                            <option value="new" @selected($message->status === 'new')>جدید</option>
                            <option value="followed" @selected($message->status === 'followed')>پیگیری‌شده</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-brand !w-full !rounded-lg !py-2.5">
                        <x-icon name="save" class="size-4" />
                        ذخیره‌ی وضعیت
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">حذف پیام</h2>
                <p class="mt-1 text-[11px] leading-6 text-slate-400">این کار پیام را برای همیشه حذف می‌کند.</p>

                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" class="mt-3" data-confirm="پیام «{{ $message->name }}» حذف شود؟">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" data-variant="destructive">
                        <x-icon name="trash" class="size-4" />
                        حذف پیام
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
