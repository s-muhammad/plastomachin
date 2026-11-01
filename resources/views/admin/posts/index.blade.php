@extends('admin.layouts.app')

@section('title', 'مقالات')

@section('content')
    <x-admin.page-header
        title="مقالات"
        description="مقاله‌ها را به‌صورت پیش‌نویس ذخیره کنید یا برای انتشار زمان‌بندی کنید."
    >
        <a href="{{ route('admin.posts.create') }}" class="btn-brand !rounded-lg !px-4 !py-2.5">
            <x-icon name="plus" class="size-4" />
            مقاله‌ی جدید
        </a>
    </x-admin.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($posts->isEmpty())
            <div class="px-5 py-16 text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="file" class="size-7" />
                </span>
                <h2 class="mt-4 text-sm font-extrabold text-navy">هنوز مقاله‌ای ثبت نشده است</h2>
                <p class="mt-1 text-xs text-slate-500">اولین راهنما یا مقاله را برای سایت بنویسید.</p>
                <a href="{{ route('admin.posts.create') }}" class="btn-brand mt-4 !rounded-lg !px-4 !py-2.5">نوشتن مقاله</a>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>مقاله</th>
                            <th class="hidden md:table-cell">تاریخ انتشار</th>
                            <th>وضعیت</th>
                            <th class="text-end">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" width="96" height="64" loading="lazy" class="h-11 w-16 shrink-0 rounded-lg border border-slate-200 object-cover">
                                        <div class="min-w-0">
                                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="block max-w-44 truncate font-bold text-navy hover:text-brand-dark md:max-w-80">{{ $post->title }}</a>
                                            <p dir="ltr" class="max-w-44 truncate text-start text-[11px] text-slate-400 md:max-w-80">{{ $post->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden text-xs text-slate-500 md:table-cell">
                                    {{ $post->published_at ? jalali_date($post->published_at, true) : '—' }}
                                </td>
                                <td>
                                    @if ($post->status === 'draft')
                                        <span class="badge border border-slate-200 bg-slate-100 text-slate-500">پیش‌نویس</span>
                                    @elseif (! $post->is_published)
                                        <span class="badge border border-brand/30 bg-brand/15 text-brand-dark">زمان‌بندی‌شده</span>
                                    @else
                                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-700">منتشرشده</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="مشاهده" title="مشاهده">
                                            <x-icon name="eye" class="size-3.5" />
                                        </a>

                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn" data-variant="outline" data-size="icon-sm" aria-label="ویرایش" title="ویرایش">
                                            <x-icon name="edit" class="size-3.5" />
                                        </a>

                                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" data-confirm="مقاله‌ی «{{ $post->title }}» حذف شود؟">
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

    {{ $posts->links() }}
@endsection
