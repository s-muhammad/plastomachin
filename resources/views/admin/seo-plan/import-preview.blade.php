@extends('admin.layouts.app')

@section('title', 'پیش‌نمایش ایمپورت CSV')

@section('content')
    <x-admin.page-header
        title="پیش‌نمایش ایمپورت CSV"
        description="فقط ردیف‌های جدید وارد می‌شوند؛ ردیف‌های تکراری و نامعتبر نادیده گرفته می‌شوند."
    >
        <a href="{{ route('admin.seo-plan.index', ['tab' => 'keywords']) }}" class="btn" data-variant="ghost">
            بازگشت
        </a>
    </x-admin.page-header>

    @php
        $cards = [
            ['label' => 'کل ردیف‌ها', 'value' => $preview['total'], 'tone' => 'text-navy'],
            ['label' => 'قابل ایمپورت', 'value' => $preview['valid'], 'tone' => 'text-emerald-600'],
            ['label' => 'تکراری', 'value' => $preview['duplicates'], 'tone' => 'text-brand-dark'],
            ['label' => 'نامعتبر', 'value' => $preview['invalid'], 'tone' => 'text-red-600'],
        ];
    @endphp

    <div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[11px] text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-extrabold {{ $card['tone'] }}">{{ fa_num($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-extrabold text-navy">فایل: {{ $filename }}</h2>
                <p class="mt-1 text-[11px] text-slate-400">پیش‌نمایش حداکثر ۵۰ ردیف اول.</p>
            </div>
        </div>

        @if ($preview['rows'] === [])
            <p class="px-5 py-12 text-center text-xs text-slate-500">ردیفی برای نمایش نیست.</p>
        @else
            <div class="table-container">
                <table class="table text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="min-w-56">کلمه</th>
                            <th class="min-w-40">صفحه هدف</th>
                            <th class="min-w-24">وضعیت</th>
                            <th class="min-w-64">توضیح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preview['rows'] as $row)
                            <tr>
                                <td class="whitespace-normal font-bold text-navy">{{ $row['keyword'] }}</td>
                                <td class="whitespace-normal text-slate-500">
                                    {{ $row['target_page'] }}
                                    @if ($row['target_url'])
                                        <span dir="ltr" class="mt-0.5 block text-[10px] text-slate-400">{{ $row['target_url'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row['state'] === 'new')
                                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">جدید</span>
                                    @elseif ($row['state'] === 'duplicate')
                                        <span class="badge border border-brand/30 bg-brand/15 text-brand-dark">تکراری</span>
                                    @else
                                        <span class="badge border border-red-200 bg-red-50 text-red-700">نامعتبر</span>
                                    @endif
                                </td>
                                <td class="whitespace-normal text-[11px] text-slate-500">{{ $row['error'] ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($preview['has_more'])
            <p class="border-t border-slate-100 px-5 py-3 text-[11px] text-slate-400">بقیه‌ی ردیف‌ها در پیش‌نمایش نمایش داده نشده‌اند اما در صورت تأیید بررسی می‌شوند.</p>
        @endif

        <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 px-5 py-4">
            <form method="POST" action="{{ route('admin.seo-plan.keywords.import') }}">
                @csrf
                <button type="submit" class="btn-brand !rounded-lg !py-2.5" @disabled($preview['valid'] === 0)>
                    <x-icon name="check" class="size-4" />
                    تأیید و ایمپورت {{ fa_num($preview['valid']) }} کلمه
                </button>
            </form>

            <a href="{{ route('admin.seo-plan.index', ['tab' => 'keywords']) }}" class="btn" data-variant="ghost">انصراف</a>
        </div>
    </div>
@endsection
