@extends('admin.layouts.app')

@section('title', 'صفحات خدماتی')

@section('content')
    <x-admin.page-header
        title="صفحات خدماتی"
        description="صفحه‌های معرفی حوزه‌های کاری. ترتیب نمایش در سایت با دکمه‌های بالا و پایین تغییر می‌کند."
    >
        <a href="{{ route('admin.services.create') }}" class="btn-brand !rounded-lg !px-4 !py-2.5">
            <x-icon name="plus" class="size-4" />
            صفحه خدماتی جدید
        </a>
    </x-admin.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($services->isEmpty())
            <div class="px-5 py-16 text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="factory" class="size-7" />
                </span>
                <h2 class="mt-4 text-sm font-extrabold text-navy">هنوز صفحه‌ای ساخته نشده است</h2>
                <p class="mt-1 text-xs text-slate-500">اولین حوزه‌ی کاری را اضافه کنید تا در سایت نمایش داده شود.</p>
                <a href="{{ route('admin.services.create') }}" class="btn-brand mt-4 !rounded-lg !px-4 !py-2.5">ساخت صفحه</a>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>صفحه</th>
                            <th class="hidden lg:table-cell">آدرس</th>
                            <th class="hidden sm:table-cell">ترتیب</th>
                            <th>وضعیت</th>
                            <th class="text-end">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="96" height="64" loading="lazy" class="h-11 w-16 shrink-0 rounded-lg border border-slate-200 object-cover">
                                        <div class="min-w-0">
                                            <a href="{{ route('services.show', $service->slug) }}" target="_blank" rel="noopener" class="block max-w-40 truncate font-bold text-navy hover:text-brand-dark sm:max-w-64">{{ $service->title }}</a>
                                            <p class="max-w-40 truncate text-[11px] text-slate-400 sm:max-w-64">{{ $service->h1 }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <span dir="ltr" class="block max-w-52 truncate text-start text-xs text-slate-500">{{ $service->slug }}</span>
                                </td>
                                <td class="hidden text-xs text-slate-500 sm:table-cell">{{ fa_num($service->sort_order) }}</td>
                                <td>
                                    @if ($service->is_active)
                                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">فعال</span>
                                    @else
                                        <span class="badge border border-slate-200 bg-slate-100 text-slate-500">غیرفعال</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="{{ route('admin.services.move', [$service, 'up']) }}">
                                            @csrf
                                            <button type="submit" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="انتقال به بالا" title="انتقال به بالا" @disabled($loop->first)>
                                                <x-icon name="arrow-up" class="size-3.5" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.services.move', [$service, 'down']) }}">
                                            @csrf
                                            <button type="submit" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="انتقال به پایین" title="انتقال به پایین" @disabled($loop->last)>
                                                <x-icon name="arrow-down" class="size-3.5" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                                            @csrf
                                            <button type="submit" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="{{ $service->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}" title="{{ $service->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                                <x-icon :name="$service->is_active ? 'eye-off' : 'eye'" class="size-3.5" />
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.services.edit', $service) }}" class="btn" data-variant="outline" data-size="icon-sm" aria-label="ویرایش" title="ویرایش">
                                            <x-icon name="edit" class="size-3.5" />
                                        </a>

                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-confirm="صفحه‌ی «{{ $service->title }}» حذف شود؟ این کار قابل بازگشت نیست.">
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

    {{ $services->links() }}
@endsection
