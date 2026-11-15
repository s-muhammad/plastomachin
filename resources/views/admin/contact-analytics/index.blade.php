@extends('admin.layouts.app')

@section('title', 'آمار تماس و واتساپ')

@section('content')
    <x-admin.page-header title="آمار تماس و واتساپ" description="تحلیل کلیک‌های کاربران روی راه‌های ارتباطی سایت." />

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">بازه گزارش</p>
        <div class="flex gap-2">
            @foreach ([7 => '۷ روز', 30 => '۳۰ روز', 90 => '۹۰ روز'] as $value => $label)
                <a href="{{ route('admin.contact-analytics', ['days' => $value]) }}"
                   class="rounded-lg border px-3 py-2 text-xs font-bold {{ $days === $value ? 'border-brand bg-brand/15 text-brand-dark' : 'border-slate-200 bg-white text-slate-600 hover:border-brand/50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">مجموع کلیک‌ها</p>
            <p class="mt-3 text-3xl font-black text-navy">{{ fa_num(number_format($total)) }}</p>
            <p class="mt-2 text-xs text-slate-400">در بازه انتخاب‌شده</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-2 text-sm text-emerald-700"><x-icon name="message-circle" class="size-4" /> کلیک واتساپ</p>
            <p class="mt-3 text-3xl font-black text-navy">{{ fa_num(number_format($whatsappCount)) }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ $total ? fa_num(round($whatsappCount / $total * 100)) : '۰' }}٪ از کل کلیک‌ها</p>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-2 text-sm text-sky-700"><x-icon name="phone" class="size-4" /> کلیک تماس</p>
            <p class="mt-3 text-3xl font-black text-navy">{{ fa_num(number_format($phoneCount)) }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ $total ? fa_num(round($phoneCount / $total * 100)) : '۰' }}٪ از کل کلیک‌ها</p>
        </div>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-5">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-3">
            <h2 class="font-extrabold text-navy">روند کلیک‌ها</h2>
            <p class="mt-1 text-xs text-slate-400">تعداد کلیک ثبت‌شده در هر روز</p>
            <div class="mt-6 flex h-52 items-end gap-1.5 border-b border-slate-100 pb-2">
                @foreach ($daily as $date => $count)
                    <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-1" title="{{ $date }}: {{ $count }}">
                        <span class="text-[9px] text-slate-500">{{ $count ?: '' }}</span>
                        <div class="w-full max-w-5 rounded-t bg-brand transition hover:bg-brand-dark"
                             style="height: {{ $count ? max(4, ($count / $maxDaily) * 100) : 2 }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between text-[10px] text-slate-400">
                <span>{{ jalali_date(now()->subDays($days - 1)) }}</span>
                <span>امروز</span>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="font-extrabold text-navy">صفحات پربازده</h2>
            <p class="mt-1 text-xs text-slate-400">صفحه‌ای که بیشترین کلیک ارتباطی را گرفته</p>
            @forelse ($topPages as $page)
                <div class="border-b border-slate-100 py-3 last:border-0">
                    <div class="flex items-center justify-between gap-3">
                        <p class="truncate text-xs text-slate-600" dir="rtl" title="{{ $page['url'] }}">{{ rawurldecode(parse_url($page['url'], PHP_URL_PATH) ?: '/') }}</p>
                        <span class="shrink-0 text-xs font-black text-navy">{{ fa_num($page['total']) }}</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-brand" style="width: {{ $topPages->first() ? ($page['total'] / max(1, $topPages->first()['total'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="py-10 text-center text-sm text-slate-400">هنوز کلیکی ثبت نشده است.</p>
            @endforelse
        </section>
    </div>

    <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-extrabold text-navy">آخرین کلیک‌ها</h2>
            <p class="mt-1 text-xs text-slate-400">۱۲ رویداد اخیر در بازه انتخاب‌شده</p>
        </div>
        @if ($latestEvents->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-slate-400">هنوز داده‌ای برای نمایش وجود ندارد.</p>
        @else
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>نوع</th><th>صفحه</th><th>منبع کمپین</th><th>زمان</th></tr></thead>
                    <tbody>
                        @foreach ($latestEvents as $event)
                            <tr>
                                <td>
                                    @if ($event->type === 'whatsapp')
                                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">واتساپ</span>
                                    @else
                                        <span class="badge border border-sky-200 bg-sky-50 text-sky-700">تماس</span>
                                    @endif
                                </td>
                                <td class="max-w-xs truncate text-xs" dir="rtl" title="{{ $event->page_url }}">{{ rawurldecode(parse_url($event->page_url, PHP_URL_PATH) ?: '/') }}</td>
                                <td class="text-xs text-slate-500">{{ $event->utm_source ?: '—' }}</td>
                                <td class="whitespace-nowrap text-xs text-slate-400">{{ jalali_date($event->created_at, true) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
