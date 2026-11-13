@extends('admin.layouts.app')

@section('title', 'برنامه سئو')

@section('content')
    <x-admin.page-header
        title="برنامه سئو"
        description="نقشه‌ی کلمات کلیدی و فهرست کارهای سئو؛ کلمات را ثبت و وضعیت کار را دنبال کنید."
    >
        @if ($tab === 'keywords')
            <button type="button" class="btn-brand !rounded-lg !px-4 !py-2.5" data-create-dialog="keyword-create-dialog">
                <x-icon name="plus" class="size-4" />
                افزودن کلمه
            </button>
            <button type="button" class="btn" data-variant="outline" data-create-dialog="keyword-import-dialog">
                <x-icon name="file" class="size-4" />
                ایمپورت CSV
            </button>
            <a href="{{ route('admin.seo-plan.keywords.export', request()->query()) }}" class="btn" data-variant="outline">
                <x-icon name="external" class="size-4" />
                خروجی CSV
            </a>
        @else
            <button type="button" class="btn-brand !rounded-lg !px-4 !py-2.5" data-create-dialog="task-create-dialog">
                <x-icon name="plus" class="size-4" />
                افزودن تسک
            </button>
            <button type="button" class="btn" data-variant="outline" data-create-dialog="task-import-dialog">
                <x-icon name="file" class="size-4" />
                ایمپورت CSV
            </button>
        @endif
    </x-admin.page-header>

    <div class="mb-4 flex flex-wrap gap-2 border-b border-slate-200">
        <a
            href="{{ route('admin.seo-plan.index', ['tab' => 'keywords']) }}"
            class="-mb-px inline-flex items-center gap-2 border-b-2 px-3 py-2 text-sm font-bold transition {{ $tab === 'keywords' ? 'border-brand text-navy' : 'border-transparent text-slate-500 hover:text-navy' }}"
        >
            کلمات کلیدی
            <span class="badge border border-slate-200 bg-slate-100 text-[10px] text-slate-500">{{ fa_num($stats['total']) }}</span>
        </a>
        <a
            href="{{ route('admin.seo-plan.index', ['tab' => 'tasks']) }}"
            class="-mb-px inline-flex items-center gap-2 border-b-2 px-3 py-2 text-sm font-bold transition {{ $tab === 'tasks' ? 'border-brand text-navy' : 'border-transparent text-slate-500 hover:text-navy' }}"
        >
            کارها
            <span class="badge border border-slate-200 bg-slate-100 text-[10px] text-slate-500">{{ fa_num($stats['tasks']) }}</span>
        </a>
    </div>

    @if ($tab === 'keywords')
        @php
            $cards = [
                ['label' => 'کل کلمات', 'value' => $stats['total'], 'icon' => 'tag', 'tone' => 'bg-brand/10 text-brand-dark'],
                ['label' => 'اولویت ۱', 'value' => $stats['priority_one'], 'icon' => 'star', 'tone' => 'bg-red-50 text-red-600'],
                ['label' => 'منتشرشده', 'value' => $stats['published'], 'icon' => 'check', 'tone' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'برنامه‌ریزی‌شده', 'value' => $stats['planned'], 'icon' => 'clock', 'tone' => 'bg-slate-100 text-slate-500'],
            ];
        @endphp

        <div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $card)
                <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $card['tone'] }}">
                        <x-icon :name="$card['icon']" class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] text-slate-400">{{ $card['label'] }}</p>
                        <p class="text-xl font-extrabold text-navy">{{ fa_num($card['value']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.seo-plan.index') }}">
                <input type="hidden" name="tab" value="keywords">
                @if (($keywordFilters['writing_plan'] ?? null) !== null)
                    <input type="hidden" name="writing_plan" value="1">
                @endif

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <div class="field sm:col-span-2">
                        <label for="filter-search">جستجو</label>
                        <input
                            id="filter-search"
                            type="search"
                            name="search"
                            value="{{ $keywordFilters['search'] ?? '' }}"
                            placeholder="کلمه، صفحه هدف یا عنوان مقاله"
                        >
                    </div>

                    <div class="field">
                        <label for="filter-status">وضعیت</label>
                        <select id="filter-status" name="status">
                            <option value="">همه</option>
                            @foreach ($options['keyword_statuses'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) ($keywordFilters['status'] ?? '') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="filter-priority">اولویت</label>
                        <select id="filter-priority" name="priority">
                            <option value="">همه</option>
                            @foreach ([1, 2, 3] as $value)
                                <option value="{{ $value }}" @selected((string) ($keywordFilters['priority'] ?? '') === (string) $value)>{{ fa_num($value) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="filter-page-type">نوع صفحه</label>
                        <select id="filter-page-type" name="page_type">
                            <option value="">همه</option>
                            @foreach ($options['page_types'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) ($keywordFilters['page_type'] ?? '') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="field">
                        <label for="filter-target-page">صفحه هدف</label>
                        <select id="filter-target-page" name="target_page">
                            <option value="">همه</option>
                            @foreach ($targetPages as $page)
                                <option value="{{ $page }}" @selected((string) ($keywordFilters['target_page'] ?? '') === (string) $page)>{{ $page }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="filter-intent">قصد جستجو</label>
                        <select id="filter-intent" name="intent">
                            <option value="">همه</option>
                            @foreach ($options['intents'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) ($keywordFilters['intent'] ?? '') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="submit" class="btn-brand !rounded-lg !px-4 !py-2.5">اعمال فیلتر</button>
                    <a href="{{ route('admin.seo-plan.index', ['tab' => 'keywords']) }}" class="btn" data-variant="ghost">پاک کردن</a>
                    <a href="{{ route('admin.seo-plan.index', ['tab' => 'keywords', 'writing_plan' => 1]) }}" class="btn" data-variant="outline">
                        <x-icon name="file" class="size-4" />
                        برنامه‌ی نوشتن
                    </a>
                    @if (($keywordFilters['writing_plan'] ?? null) !== null)
                        <span class="badge border border-brand/30 bg-brand/15 text-brand-dark">فیلتر فعال: برنامه‌ریزی‌شده + اولویت ۱</span>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if ($keywords->isEmpty())
                <div class="px-5 py-16 text-center">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <x-icon name="tag" class="size-7" />
                    </span>
                    <h2 class="mt-4 text-sm font-extrabold text-navy">کلمه‌ای با این فیلتر پیدا نشد</h2>
                    <p class="mt-1 text-xs text-slate-500">فیلترها را پاک کنید یا اولین کلمه را اضافه کنید.</p>
                </div>
            @else
                @php
                    $baseQuery = collect(request()->query())->except('page')->all();
                    $currentSort = request('sort', 'priority');
                    $currentDirection = request('direction') === 'desc' ? 'desc' : 'asc';
                    $sortUrl = function (string $column) use ($baseQuery, $currentSort, $currentDirection) {
                        $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

                        return route('admin.seo-plan.index', array_merge($baseQuery, [
                            'tab' => 'keywords',
                            'sort' => $column,
                            'direction' => $direction,
                        ]));
                    };
                    $headers = [
                        ['keyword', 'کلمه', 'min-w-48'],
                        ['role', 'نقش', 'min-w-28'],
                        ['target_page', 'صفحه هدف', 'min-w-40'],
                        ['page_type', 'نوع صفحه', 'min-w-32'],
                        // ['intent', 'قصد', 'min-w-24'],
                        ['priority', 'اولویت', 'min-w-20'],
                        // ['volume', 'حجم', 'min-w-24'],
                        // ['competition', 'رقابت', 'min-w-24'],
                        ['position', 'رتبه', 'min-w-20'],
                        ['status', 'وضعیت', 'min-w-36'],
                    ];
                @endphp

                <div class="table-container">
                    <table class="table text-xs">
                        <thead class="bg-slate-50">
                            <tr>
                                @foreach ($headers as [$column, $label, $class])
                                    <th class="{{ $class }}">
                                        <a href="{{ $sortUrl($column) }}" class="inline-flex items-center gap-1 hover:text-brand-dark">
                                            {{ $label }}
                                            @if ($currentSort === $column)
                                                <x-icon :name="$currentDirection === 'asc' ? 'arrow-up' : 'arrow-down'" class="size-3" />
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                                <th class="">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keywords as $keyword)
                                @php
                                    $editRecord = [
                                        '_edit_id' => $keyword->id,
                                        'keyword' => $keyword->keyword,
                                        'role' => $keyword->role,
                                        'target_page' => $keyword->target_page,
                                        'target_url' => $keyword->target_url,
                                        'page_type' => $keyword->page_type,
                                        // 'intent' => $keyword->intent,
                                        'priority' => (string) $keyword->priority,
                                        // 'volume' => $keyword->volume,
                                        // 'competition' => $keyword->competition,
                                        'position' => $keyword->position,
                                        'status' => $keyword->status,
                                        'content_title' => $keyword->content_title,
                                        'secondary_keywords' => $keyword->secondary_keywords,
                                        'internal_links' => $keyword->internal_links,
                                        'notes' => $keyword->notes,
                                    ];
                                @endphp

                                <tr class="{{ $keyword->status === 'published' ? 'bg-emerald-50/30' : '' }}">
                                    <td class="whitespace-normal">
                                        <p class="max-w-64 font-bold text-navy">{{ $keyword->keyword }}</p>
                                        @if ($keyword->content_title)
                                            <p class="mt-0.5 max-w-64 truncate text-[10px] text-slate-400">{{ $keyword->content_title }}</p>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge border border-slate-200 bg-slate-100 text-[10px] text-slate-500">{{ $keyword->role_label }}</span>
                                    </td>

                                    <td class="whitespace-normal">
                                        <span class="block max-w-48 truncate text-xs text-slate-600">{{ $keyword->target_page ?: '—' }}</span>
                                        @if ($keyword->safe_target_url)
                                            <a href="{{ $keyword->safe_target_url }}" target="_blank" rel="noopener" class="mt-0.5 inline-flex items-center gap-1 text-[10px] text-brand-dark hover:underline">
                                                <x-icon name="external" class="size-3" />
                                                مشاهده
                                            </a>
                                        @endif
                                    </td>

                                    <td class="text-[11px] text-slate-500">{{ $keyword->page_type_label }}</td>
                                    {{-- <td class="text-[11px] text-slate-500">{{ $keyword->intent_label }}</td> --}}

                                    <td>
                                        <form method="POST" action="{{ route('admin.seo-plan.keywords.inline', $keyword) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="priority" class="!h-8 !w-20 !text-xs" onchange="this.form.requestSubmit()" aria-label="اولویت">
                                                @foreach ([1, 2, 3] as $priority)
                                                    <option value="{{ $priority }}" @selected($keyword->priority === $priority)>{{ fa_num($priority) }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    {{-- <td>
                                        <form method="POST" action="{{ route('admin.seo-plan.keywords.inline', $keyword) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="number"
                                                name="volume"
                                                value="{{ $keyword->volume }}"
                                                min="0"
                                                class="input !h-8 !w-24 !text-xs"
                                                onblur="this.form.requestSubmit()"
                                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.form.requestSubmit(); }"
                                                aria-label="حجم جستجو"
                                            >
                                        </form>
                                    </td> --}}

                                    {{-- <td>
                                        <form method="POST" action="{{ route('admin.seo-plan.keywords.inline', $keyword) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="competition" class="!h-8 !w-24 !text-xs" onchange="this.form.requestSubmit()" aria-label="رقابت">
                                                <option value="">—</option>
                                                @foreach ($options['competitions'] as $value => $label)
                                                    <option value="{{ $value }}" @selected((string) $keyword->competition === (string) $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td> --}}

                                    <td>
                                        <form method="POST" action="{{ route('admin.seo-plan.keywords.inline', $keyword) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="number"
                                                name="position"
                                                value="{{ $keyword->position }}"
                                                min="0"
                                                class="input !h-8 !w-20 !text-xs"
                                                onblur="this.form.requestSubmit()"
                                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.form.requestSubmit(); }"
                                                aria-label="رتبه"
                                            >
                                        </form>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('admin.seo-plan.keywords.inline', $keyword) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="!h-8 !w-36 !text-xs" onchange="this.form.requestSubmit()" aria-label="وضعیت">
                                                @foreach ($options['keyword_statuses'] as $value => $label)
                                                    <option value="{{ $value }}" @selected((string) $keyword->status === (string) $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="flex items-center gap-1">
                                            @if ($keyword->page_type === 'blog')
                                                <form method="POST" action="{{ route('admin.seo-plan.keywords.task', $keyword) }}">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="btn"
                                                        data-variant="ghost"
                                                        data-size="icon-sm"
                                                        title="{{ $keyword->open_tasks_count ? 'تسک باز دارد' : 'ساخت تسک نوشتن مقاله' }}"
                                                        @disabled($keyword->open_tasks_count)
                                                    >
                                                        <x-icon name="file" class="size-3.5" />
                                                    </button>
                                                </form>
                                            @endif

                                            <button
                                                type="button"
                                                class="btn"
                                                data-variant="outline"
                                                data-size="icon-sm"
                                                data-edit-dialog="keyword-edit-dialog"
                                                data-action="{{ route('admin.seo-plan.keywords.update', $keyword) }}"
                                                data-record="{{ json_encode($editRecord, JSON_UNESCAPED_UNICODE) }}"
                                                aria-label="ویرایش کلمه"
                                                title="ویرایش کلمه"
                                            >
                                                <x-icon name="edit" class="size-3.5" />
                                            </button>

                                            <form method="POST" action="{{ route('admin.seo-plan.keywords.destroy', $keyword) }}" data-confirm="کلمه‌ی «{{ $keyword->keyword }}» حذف شود؟">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn" data-variant="destructive" data-size="icon-sm" aria-label="حذف کلمه" title="حذف کلمه">
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

        {{ $keywords->links() }}

        <dialog id="keyword-create-dialog" class="dialog">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">افزودن کلمه کلیدی</h2>
                    <p class="mt-1 text-[11px] text-slate-400">کلمه، نقش و صفحه‌ی هدف را مشخص کنید.</p>
                </header>
                <section class="max-h-[70vh] overflow-y-auto pe-1">
                    @include('admin.seo-plan._keyword-form', ['keyword' => null, 'prefix' => 'keyword-create', 'mode' => 'create'])
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        <dialog id="keyword-edit-dialog" class="dialog">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">ویرایش کلمه کلیدی</h2>
                    <p class="mt-1 text-[11px] text-slate-400">همه‌ی فیلدها قابل ویرایش هستند.</p>
                </header>
                <section class="max-h-[70vh] overflow-y-auto pe-1">
                    @include('admin.seo-plan._keyword-form', ['keyword' => null, 'prefix' => 'keyword-edit', 'mode' => 'edit', 'action' => '#'])
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        <dialog id="keyword-import-dialog" class="dialog">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">ایمپورت CSV</h2>
                    <p class="mt-1 text-[11px] text-slate-400">ابتدا پیش‌نمایش را ببینید؛ ردیف‌های تکراری وارد نمی‌شوند.</p>
                </header>
                <section>
                    <form method="POST" action="{{ route('admin.seo-plan.keywords.import.preview') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <x-admin.field
                            name="file"
                            label="فایل CSV"
                            type="file"
                            accept=".csv,text/csv"
                            required
                            help="UTF-8 بدون BOM؛ حداکثر ۵ مگابایت."
                        />

                        <p class="rounded-xl bg-slate-50 p-3 text-[11px] leading-6 text-slate-500">
                            ستون‌ها: keyword, role, target_page, target_url, page_type, intent, priority,
                            content_title, secondary_keywords, internal_links, status, notes
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="submit" class="btn-brand !rounded-lg !py-2.5">پیش‌نمایش</button>
                            <button type="button" class="btn" data-variant="ghost" onclick="this.closest('dialog').close()">انصراف</button>
                        </div>
                    </form>
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        @if ($errors->any() && old('_form') === 'keyword-create')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('keyword-create-dialog')?.showModal();
                });
            </script>
        @elseif ($errors->any() && old('_form') === 'keyword-edit')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const dialog = document.getElementById('keyword-edit-dialog');
                    const form = dialog?.querySelector('form');
                    const id = @json(old('_edit_id'));

                    if (!dialog || !form || !id) return;

                    form.action = @json(url('/admin/seo-plan/keywords')).replace(/\/$/, '') + '/' + id;
                    dialog.showModal();
                });
            </script>
        @endif
    @else
        @php
            $taskBaseQuery = collect(request()->query())->except('page')->all();
        @endphp

        <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.seo-plan.index') }}">
                <input type="hidden" name="tab" value="tasks">

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="field lg:col-span-2">
                        <label for="task-filter-search">جستجو</label>
                        <input
                            id="task-filter-search"
                            type="search"
                            name="task_search"
                            value="{{ $taskFilters['search'] ?? '' }}"
                            placeholder="عنوان یا توضیح تسک"
                        >
                    </div>

                    <div class="field">
                        <label for="task-filter-category">دسته</label>
                        <select id="task-filter-category" name="task_category">
                            <option value="">همه</option>
                            @foreach ($options['categories'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) ($taskFilters['category'] ?? '') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="task-filter-priority">اولویت</label>
                        <select id="task-filter-priority" name="task_priority">
                            <option value="">همه</option>
                            @foreach ([1, 2, 3] as $value)
                                <option value="{{ $value }}" @selected((string) ($taskFilters['priority'] ?? '') === (string) $value)>{{ fa_num($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="submit" class="btn-brand !rounded-lg !px-4 !py-2.5">اعمال فیلتر</button>
                    <a href="{{ route('admin.seo-plan.index', ['tab' => 'tasks']) }}" class="btn" data-variant="ghost">پاک کردن</a>
                </div>
            </form>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            @php
                $columns = [
                    'todo' => ['label' => 'انجام نشده', 'icon' => 'clock', 'tone' => 'border-slate-200 bg-slate-50/70'],
                    'doing' => ['label' => 'در حال انجام', 'icon' => 'refresh', 'tone' => 'border-brand/30 bg-brand/5'],
                    'done' => ['label' => 'انجام شد', 'icon' => 'check', 'tone' => 'border-emerald-200 bg-emerald-50/40'],
                ];
            @endphp

            @foreach ($columns as $column => $meta)
                <section class="rounded-2xl border {{ $meta['tone'] }}">
                    <header class="flex items-center justify-between gap-2 border-b border-slate-200 px-4 py-3">
                        <h3 class="inline-flex items-center gap-2 text-sm font-extrabold text-navy">
                            <x-icon :name="$meta['icon']" class="size-4 text-slate-400" />
                            {{ $meta['label'] }}
                        </h3>
                        <span class="badge border border-slate-200 bg-white text-[10px] text-slate-500">{{ fa_num($column === 'done' ? $doneCount : $tasks[$column]->count()) }}</span>
                    </header>

                    <div class="space-y-3 p-3">
                        @forelse ($tasks[$column] as $task)
                            @include('admin.seo-plan._task-card', ['task' => $task, 'column' => $column])
                        @empty
                            <p class="px-2 py-10 text-center text-xs text-slate-400">تسکی در این ستون نیست.</p>
                        @endforelse
                    </div>

                    @if ($column === 'done')
                        @if ($doneCount > $tasks['done']->count())
                            <div class="border-t border-slate-200 p-3 text-center">
                                <a href="{{ route('admin.seo-plan.index', array_merge($taskBaseQuery, ['tab' => 'tasks', 'all_done' => 1])) }}" class="btn" data-variant="ghost" data-size="sm">
                                    نمایش همه ({{ fa_num($doneCount) }})
                                </a>
                            </div>
                        @elseif ($allDone)
                            <div class="border-t border-slate-200 p-3 text-center">
                                <a href="{{ route('admin.seo-plan.index', array_merge(collect($taskBaseQuery)->except('all_done')->all(), ['tab' => 'tasks'])) }}" class="btn" data-variant="ghost" data-size="sm">
                                    فقط ۱۰ مورد آخر
                                </a>
                            </div>
                        @endif
                    @endif
                </section>
            @endforeach
        </div>

        <dialog id="task-create-dialog" class="dialog">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">افزودن تسک</h2>
                    <p class="mt-1 text-[11px] text-slate-400">کار جدید را با دسته و اولویت مشخص کنید.</p>
                </header>
                <section class="max-h-[70vh] overflow-y-auto pe-1">
                    @include('admin.seo-plan._task-form', ['task' => null, 'prefix' => 'task-create', 'mode' => 'create'])
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        <dialog id="task-edit-dialog" class="dialog">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">ویرایش تسک</h2>
                    <p class="mt-1 text-[11px] text-slate-400">وضعیت، سررسید و توضیح تسک را تغییر دهید.</p>
                </header>
                <section class="max-h-[70vh] overflow-y-auto pe-1">
                    @include('admin.seo-plan._task-form', ['task' => null, 'prefix' => 'task-edit', 'mode' => 'edit', 'action' => '#'])
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        <dialog id="task-import-dialog" class="dialog">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
                <header class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-navy">ایمپورت CSV تسک‌ها</h2>
                    <p class="mt-1 text-[11px] text-slate-400">ابتدا پیش‌نمایش را ببینید؛ تسک‌های تکراری وارد نمی‌شوند.</p>
                </header>
                <section>
                    <form method="POST" action="{{ route('admin.seo-plan.tasks.import.preview') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_form" value="task-import">
                        <x-admin.field
                            name="file"
                            label="فایل CSV"
                            type="file"
                            accept=".csv,text/csv"
                            required
                            help="UTF-8 بدون BOM؛ حداکثر ۵ مگابایت."
                        />

                        <p class="rounded-xl bg-slate-50 p-3 text-[11px] leading-6 text-slate-500">
                            ستون‌ها: title, description, category, priority, status
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="submit" class="btn-brand !rounded-lg !py-2.5">پیش‌نمایش</button>
                            <button type="button" class="btn" data-variant="ghost" onclick="this.closest('dialog').close()">انصراف</button>
                        </div>
                    </form>
                </section>
                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" aria-label="بستن" onclick="this.closest('dialog').close()">
                    <x-icon name="close" class="size-4" />
                </button>
            </div>
        </dialog>

        @if ($errors->any() && old('_form') === 'task-create')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('task-create-dialog')?.showModal();
                });
            </script>
        @elseif ($errors->any() && old('_form') === 'task-edit')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const dialog = document.getElementById('task-edit-dialog');
                    const form = dialog?.querySelector('form');
                    const id = @json(old('_edit_id'));

                    if (!dialog || !form || !id) return;

                    form.action = @json(url('/admin/seo-plan/tasks')).replace(/\/$/, '') + '/' + id;
                    dialog.showModal();
                });
            </script>
        @elseif ($errors->any() && old('_form') === 'task-import')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('task-import-dialog')?.showModal();
                });
            </script>
        @endif
    @endif
@endsection
