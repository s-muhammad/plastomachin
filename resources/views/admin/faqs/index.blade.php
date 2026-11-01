@extends('admin.layouts.app')

@section('title', 'سوالات متداول')

@section('content')
    <x-admin.page-header
        title="سوالات متداول عمومی"
        description="این سوال‌ها در صفحه‌ی «سوالات متداول» و داده‌های ساختاریافته‌ی FAQ نمایش داده می‌شوند. سوال‌های هر صفحه‌ی خدماتی از همان صفحه مدیریت می‌شود."
    >
        <a href="{{ route('admin.faqs.create') }}" class="btn-brand !rounded-lg !px-4 !py-2.5">
            <x-icon name="plus" class="size-4" />
            سوال جدید
        </a>
    </x-admin.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($faqs->isEmpty())
            <div class="px-5 py-16 text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="message" class="size-7" />
                </span>
                <h2 class="mt-4 text-sm font-extrabold text-navy">هنوز سوالی ثبت نشده است</h2>
                <p class="mt-1 text-xs text-slate-500">پرسش‌های پرتکرار مشتریان را اضافه کنید.</p>
                <a href="{{ route('admin.faqs.create') }}" class="btn-brand mt-4 !rounded-lg !px-4 !py-2.5">افزودن سوال</a>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>سوال</th>
                            <th>پاسخ</th>
                            <th>ترتیب</th>
                            <th class="text-end">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $faq)
                            <tr>
                                <td class="max-w-80 whitespace-normal">
                                    <p class="font-bold text-navy">{{ $faq->question }}</p>
                                </td>
                                <td class="max-w-96 whitespace-normal">
                                    <p class="line-clamp-2 text-xs leading-6 text-slate-500">{{ \Illuminate\Support\Str::limit($faq->answer, 120) }}</p>
                                </td>
                                <td class="text-xs text-slate-500">{{ fa_num($faq->sort_order) }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn" data-variant="outline" data-size="icon-sm" aria-label="ویرایش" title="ویرایش">
                                            <x-icon name="edit" class="size-3.5" />
                                        </a>

                                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" data-confirm="این سوال حذف شود؟">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" data-variant="destructive" data-size="icon-sm" aria-label="حذف" title="حذف">
                                                <x-icon name="trash" class="size-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $faqs->links() }}
@endsection
