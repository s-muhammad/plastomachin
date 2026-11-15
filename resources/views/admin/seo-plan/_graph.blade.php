@php
    $graphNodes = collect($graph['nodes']);
    $graphCards = [
        ['label' => 'کلمات در گراف', 'value' => $graphNodes->count(), 'icon' => 'tag', 'tone' => 'bg-brand/10 text-brand-dark'],
        ['label' => 'ارتباط متنی', 'value' => count($graph['related']), 'icon' => 'handshake', 'tone' => 'bg-indigo-50 text-indigo-500'],
        ['label' => 'اولویت ۱', 'value' => $graphNodes->where('priority', 1)->count(), 'icon' => 'star', 'tone' => 'bg-red-50 text-red-600'],
        ['label' => 'منتشرشده', 'value' => $graphNodes->where('status', 'published')->count(), 'icon' => 'check', 'tone' => 'bg-emerald-50 text-emerald-600'],
    ];
@endphp

<div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($graphCards as $card)
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
        <input type="hidden" name="tab" value="graph">

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div class="field sm:col-span-2">
                <label for="graph-filter-search">جستجو</label>
                <input
                    id="graph-filter-search"
                    type="search"
                    name="search"
                    value="{{ $keywordFilters['search'] ?? '' }}"
                    placeholder="کلمه، صفحه هدف یا عنوان مقاله"
                >
            </div>

            <div class="field">
                <label for="graph-filter-status">وضعیت</label>
                <select id="graph-filter-status" name="status">
                    <option value="">همه</option>
                    @foreach ($options['keyword_statuses'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) ($keywordFilters['status'] ?? '') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="graph-filter-priority">اولویت</label>
                <select id="graph-filter-priority" name="priority">
                    <option value="">همه</option>
                    @foreach ([1, 2, 3] as $value)
                        <option value="{{ $value }}" @selected((string) ($keywordFilters['priority'] ?? '') === (string) $value)>{{ fa_num($value) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="graph-filter-page-type">نوع صفحه</label>
                <select id="graph-filter-page-type" name="page_type">
                    <option value="">همه</option>
                    @foreach ($options['page_types'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) ($keywordFilters['page_type'] ?? '') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="graph-filter-target-page">صفحه هدف</label>
                <select id="graph-filter-target-page" name="target_page">
                    <option value="">همه</option>
                    @foreach ($targetPages as $page)
                        <option value="{{ $page }}" @selected((string) ($keywordFilters['target_page'] ?? '') === (string) $page)>{{ $page }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button type="submit" class="btn-brand !rounded-lg !px-4 !py-2.5">اعمال فیلتر</button>
            <a href="{{ route('admin.seo-plan.index', ['tab' => 'graph']) }}" class="btn" data-variant="ghost">پاک کردن</a>
            <span class="text-[11px] text-slate-400">با کلیک روی هر کلمه، بریف کامل آن در کنار گراف نمایش داده می‌شود.</span>
        </div>
    </form>
</div>

@if ($graph['truncated'])
    <div class="mb-4 flex flex-wrap items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
        <x-icon name="clock" class="size-4" />
        برای خوانایی گراف فقط {{ fa_num($graphNodes->count()) }} کلمه از {{ fa_num($graph['total']) }} کلمه‌ی این فیلتر نمایش داده می‌شود؛ برای دیدن بقیه از جستجو یا فیلتر استفاده کنید.
    </div>
@endif

<div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_23rem]" data-seo-graph>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 px-4 py-3">
            <div>
                <h2 class="text-sm font-extrabold text-navy">گراف کلمات کلیدی</h2>
                <p class="mt-0.5 text-[11px] text-slate-400" data-graph-count>در حال ساخت گراف…</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-500">
                    گروه‌بندی
                    <select data-graph-group class="input !h-8 !w-36 !text-xs">
                        <option value="page">صفحه هدف</option>
                        <option value="page_type">نوع صفحه</option>
                        <option value="role">نقش کلمه</option>
                    </select>
                </label>

                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1.5 text-[11px] text-slate-500">
                    <input type="checkbox" data-graph-related class="size-3.5 accent-brand" checked>
                    ارتباط متنی
                </label>

                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1.5 text-[11px] text-slate-500">
                    <input type="checkbox" data-graph-labels class="size-3.5 accent-brand">
                    برچسب‌ها
                </label>

                <button type="button" class="btn" data-variant="outline" data-size="sm" data-graph-reset>
                    <x-icon name="refresh" class="size-3.5" />
                    بازچینش
                </button>
            </div>
        </header>

        @if ($graphNodes->isEmpty())
            <div class="flex h-[620px] flex-col items-center justify-center gap-2 px-5 text-center xl:h-[680px]">
                <span class="flex size-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="grid" class="size-7" />
                </span>
                <h3 class="mt-2 text-sm font-extrabold text-navy">کلمه‌ای با این فیلتر پیدا نشد</h3>
                <p class="text-xs text-slate-500">فیلترها را پاک کنید تا گراف کلمات ساخته شود.</p>
            </div>
        @else
            <div class="relative">
                <svg
                    data-graph-svg
                    class="block h-[620px] w-full cursor-grab touch-none select-none xl:h-[680px]"
                    role="group"
                    aria-label="گراف ارتباط کلمات کلیدی برنامه سئو"
                ></svg>

                <div class="pointer-events-none absolute inset-x-0 top-0 flex items-start justify-between gap-2 p-3">
                    <p class="pointer-events-none max-w-56 rounded-lg bg-white/85 px-2 py-1 text-[10px] leading-5 text-slate-400 backdrop-blur">
                        برای جابه‌جایی بکشید، با اسکرول زوم کنید و روی کلمه کلیک کنید.
                    </p>

                    <div class="pointer-events-auto flex items-center gap-1 rounded-lg border border-slate-200 bg-white/90 p-1 shadow-sm backdrop-blur">
                        <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" data-graph-zoom-in aria-label="بزرگ‌نمایی" title="بزرگ‌نمایی">
                            <x-icon name="plus" class="size-3.5" />
                        </button>
                        <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" data-graph-zoom-out aria-label="کوچک‌نمایی" title="کوچک‌نمایی">
                            <x-icon name="minus" class="size-3.5" />
                        </button>
                        <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" data-graph-fit aria-label="نمایش کامل" title="نمایش کامل">
                            <x-icon name="grid" class="size-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <footer class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-100 px-4 py-3 text-[10px] text-slate-500">
                <span class="font-bold text-slate-600">راهنما:</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full bg-slate-400"></span> برنامه‌ریزی‌شده</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full bg-brand"></span> در حال نوشتن</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full bg-emerald-500"></span> منتشرشده</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full bg-rose-500"></span> نیاز به بازبینی</span>
                <span class="inline-flex items-center gap-1"><span class="h-0.5 w-5 rounded bg-brand/60"></span> گروه</span>
                <span class="inline-flex items-center gap-1"><span class="h-0.5 w-5 rounded border-t border-dashed border-indigo-400"></span> ارتباط متنی</span>
                <span class="inline-flex items-center gap-1"><span class="size-2.5 rounded-full border-2 border-navy"></span> اندازه = اولویت</span>
            </footer>
        @endif
    </section>

    <aside class="h-fit rounded-2xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-4" data-brief>
        <div data-brief-empty class="flex flex-col items-center px-5 py-14 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <x-icon name="file" class="size-6" />
            </span>
            <h2 class="mt-3 text-sm font-extrabold text-navy">بریف کلمه</h2>
            <p class="mt-1 max-w-60 text-[11px] leading-6 text-slate-500">
                روی یکی از کلمه‌ها در گراف کلیک کنید تا نقش، صفحه هدف، عنوان مقاله، کلمات فرعی، لینک‌های داخلی و تسک‌های آن را ببینید.
            </p>
        </div>

        <div data-brief-content class="hidden">
            <header class="border-b border-slate-100 p-4">
                <p class="text-[10px] font-bold text-brand-dark">بریف کلمه کلیدی</p>
                <h2 data-brief-keyword class="mt-1 text-sm font-extrabold leading-7 text-navy"></h2>
                <div class="mt-2 flex flex-wrap items-center gap-1">
                    <span data-brief-status class="badge border text-[10px]"></span>
                    <span data-brief-priority class="badge border border-slate-200 bg-slate-50 text-[10px] text-slate-600"></span>
                    <span data-brief-role class="badge border border-slate-200 bg-slate-50 text-[10px] text-slate-600"></span>
                </div>
            </header>

            <div class="space-y-4 p-4">
                <dl class="grid grid-cols-2 gap-3 text-[11px]">
                    <div>
                        <dt class="text-slate-400">صفحه هدف</dt>
                        <dd data-brief-page class="mt-0.5 font-bold text-slate-700">—</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">نوع صفحه</dt>
                        <dd data-brief-page-type class="mt-0.5 font-bold text-slate-700">—</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">رتبه فعلی</dt>
                        <dd data-brief-position class="mt-0.5 font-bold text-slate-700">—</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">تسک باز</dt>
                        <dd data-brief-open-tasks class="mt-0.5 font-bold text-slate-700">—</dd>
                    </div>
                </dl>

                <div data-brief-title-wrap class="hidden">
                    <p class="text-[11px] text-slate-400">عنوان مقاله</p>
                    <p data-brief-title class="mt-1 rounded-lg bg-slate-50 p-2 text-[11px] leading-6 text-slate-700"></p>
                </div>

                <div data-brief-secondary-wrap class="hidden">
                    <p class="text-[11px] text-slate-400">کلمات فرعی</p>
                    <div data-brief-secondary class="mt-1.5 flex flex-wrap gap-1"></div>
                </div>

                <div data-brief-internal-wrap class="hidden">
                    <p class="text-[11px] text-slate-400">لینک‌های داخلی</p>
                    <div data-brief-internal class="mt-1.5 flex flex-wrap gap-1"></div>
                </div>

                <div data-brief-notes-wrap class="hidden">
                    <p class="text-[11px] text-slate-400">یادداشت</p>
                    <p data-brief-notes class="mt-1 whitespace-pre-line rounded-lg bg-slate-50 p-2 text-[11px] leading-6 text-slate-600"></p>
                </div>

                <div>
                    <p class="text-[11px] text-slate-400">تسک‌ها</p>
                    <ul data-brief-tasks class="mt-1.5 space-y-1.5"></ul>
                    <p data-brief-tasks-empty class="mt-1.5 text-[11px] text-slate-400">تسکی به این کلمه متصل نیست.</p>
                </div>

                <a data-brief-target-url href="#" target="_blank" rel="noopener" class="hidden items-center gap-1 text-[11px] font-bold text-brand-dark hover:underline">
                    <x-icon name="external" class="size-3.5" />
                    مشاهده صفحه هدف
                </a>
            </div>

            <footer class="flex flex-wrap items-center gap-2 border-t border-slate-100 p-4">
                <a data-brief-table href="{{ route('admin.seo-plan.index', ['tab' => 'keywords']) }}" class="btn" data-variant="outline" data-size="sm">
                    <x-icon name="grid" class="size-3.5" />
                    جدول کلمات
                </a>

                <form data-brief-task-form method="POST" action="#" class="hidden">
                    @csrf
                    <button type="submit" class="btn-brand !rounded-lg !px-3 !py-2 !text-xs">
                        <x-icon name="plus" class="size-3.5" />
                        ساخت تسک مقاله
                    </button>
                </form>

                <button type="button" class="btn" data-variant="ghost" data-size="sm" data-brief-copy>
                    <x-icon name="file" class="size-3.5" />
                    <span data-brief-copy-label>کپی بریف</span>
                </button>
            </footer>
        </div>

        <div data-brief-group class="hidden">
            <header class="border-b border-slate-100 p-4">
                <p data-group-mode class="text-[10px] font-bold text-brand-dark"></p>
                <h2 data-group-title class="mt-1 text-sm font-extrabold leading-7 text-navy"></h2>
                <p data-group-count class="mt-1 text-[11px] text-slate-400"></p>
            </header>
            <div class="p-4">
                <p class="text-[11px] text-slate-400">کلمات این گروه</p>
                <div data-group-keywords class="mt-2 flex flex-wrap gap-1"></div>
            </div>
        </div>
    </aside>
</div>

<template data-brief-chip-template>
    <span class="badge border border-slate-200 bg-slate-50 text-[10px] font-normal text-slate-600"></span>
</template>

<template data-brief-keyword-chip-template>
    <button type="button" class="badge border border-brand/30 bg-brand/10 text-[10px] font-normal text-brand-dark transition hover:bg-brand/20"></button>
</template>

<template data-brief-task-template>
    <li class="rounded-lg border border-slate-200 bg-slate-50/60 p-2">
        <p data-task-title class="text-[11px] font-bold leading-5 text-navy"></p>
        <p class="mt-1 flex flex-wrap items-center gap-1">
            <span data-task-status class="badge border text-[10px]"></span>
            <span data-task-category class="badge border border-slate-200 bg-white text-[10px] text-slate-500"></span>
            <span data-task-due class="badge border border-slate-200 bg-white text-[10px] text-slate-500"></span>
        </p>
    </li>
</template>

<script type="application/json" id="seo-graph-data">{!! json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
