@extends('admin.layouts.app')

@section('title', 'پیام‌های دریافتی')

@section('content')
    <x-admin.page-header
        title="پیام‌های دریافتی"
        description="پیام‌های فرم تماس سایت. بعد از تماس با مشتری، وضعیت پیام را به «پیگیری‌شده» تغییر دهید."
    >
        <a href="{{ route('admin.messages.index', ['status' => 'new']) }}" class="btn" data-variant="outline">
            <x-icon name="inbox" class="size-4" />
            فقط پیام‌های جدید
            @if ($newCount > 0)
                <span class="badge" data-variant="primary">{{ fa_num($newCount) }}</span>
            @endif
        </a>
    </x-admin.page-header>

    <div class="mb-4 flex flex-wrap items-center gap-1.5">
        <a href="{{ route('admin.messages.index') }}" class="btn" data-variant="{{ $status ? 'ghost' : 'primary' }}" data-size="sm">همه</a>
        <a href="{{ route('admin.messages.index', ['status' => 'new']) }}" class="btn" data-variant="{{ $status === 'new' ? 'primary' : 'ghost' }}" data-size="sm">
            جدید
            @if ($newCount > 0)
                <span class="badge" data-variant="secondary">{{ fa_num($newCount) }}</span>
            @endif
        </a>
        <a href="{{ route('admin.messages.index', ['status' => 'followed']) }}" class="btn" data-variant="{{ $status === 'followed' ? 'primary' : 'ghost' }}" data-size="sm">پیگیری‌شده</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($messages->isEmpty())
            <div class="px-5 py-16 text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="inbox" class="size-7" />
                </span>
                <h2 class="mt-4 text-sm font-extrabold text-navy">پیامی در این دسته وجود ندارد</h2>
                <p class="mt-1 text-xs text-slate-500">پیام‌های جدید فرم تماس اینجا نمایش داده می‌شوند.</p>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>فرستنده</th>
                            <th>شماره تماس</th>
                            <th class="hidden xl:table-cell">صفحه‌ی مبدأ</th>
                            <th class="hidden md:table-cell">تاریخ</th>
                            <th>وضعیت</th>
                            <th class="text-end">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr class="{{ $message->status === 'new' ? 'bg-brand/[0.03]' : '' }}">
                                <td>
                                    <a href="{{ route('admin.messages.show', $message) }}" class="block max-w-44 truncate font-bold text-navy hover:text-brand-dark sm:max-w-64">{{ $message->name }}</a>
                                    <p class="max-w-44 truncate text-[11px] text-slate-400 sm:max-w-64">
                                        {{ $message->message ? \Illuminate\Support\Str::limit($message->message, 55) : 'بدون متن پیام' }}
                                    </p>
                                </td>
                                <td class="text-start text-xs text-slate-500">
                                    <a href="{{ \App\Support\Site::phoneHref($message->phone) }}" dir="ltr" class="hover:text-brand-dark">{{ \App\Support\Site::phoneDisplay($message->phone) }}</a>
                                </td>
                                <td class="hidden xl:table-cell">
                                    <span dir="ltr" class="block max-w-48 truncate text-start text-[11px] text-slate-400">{{ $message->source_page ?: '—' }}</span>
                                </td>
                                <td class="hidden text-xs text-slate-400 md:table-cell">{{ jalali_date($message->created_at, true) }}</td>
                                <td>
                                    @if ($message->status === 'new')
                                        <span class="badge border border-brand/30 bg-brand/15 text-brand-dark">جدید</span>
                                    @else
                                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">پیگیری‌شده</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ \App\Support\Site::phoneHref($message->phone) }}" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="تماس" title="تماس">
                                            <x-icon name="phone" class="size-3.5" />
                                        </a>
                                        <a href="{{ \App\Support\Site::whatsappUrl($message->phone, 'سلام، درباره‌ی پیام شما در سایت پلاستو ماشین تماس می‌گیریم.') }}" target="_blank" rel="noopener" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="واتساپ" title="واتساپ">
                                            <x-icon name="whatsapp" class="size-3.5 text-whatsapp-dark" />
                                        </a>
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn" data-variant="outline" data-size="sm">جزئیات</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $messages->links() }}
@endsection
