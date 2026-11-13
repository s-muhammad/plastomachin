@php
    $editRecord = [
        '_edit_id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'category' => $task->category,
        'priority' => (string) $task->priority,
        'status' => $task->status,
        'keyword_id' => (string) ($task->keyword_id ?? ''),
        'due_date' => $task->due_date?->format('Y-m-d'),
    ];
@endphp

<article class="rounded-xl border bg-white p-3 shadow-sm {{ $task->is_overdue ? 'border-red-200' : 'border-slate-200' }}">
    <div class="flex items-start justify-between gap-2">
        <h4 class="text-xs font-extrabold leading-6 text-navy">{{ $task->title }}</h4>
        <span class="badge shrink-0 border border-slate-200 bg-slate-100 text-[10px] text-slate-500">{{ $task->category_label }}</span>
    </div>

    @if ($task->description)
        <div class="mt-2 text-[11px] leading-6 text-slate-500">{!! nl2br(e($task->description)) !!}</div>
    @endif

    <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[10px]">
        <span class="badge border border-slate-200 bg-slate-100 text-slate-500">اولویت {{ fa_num($task->priority) }}</span>

        @if ($task->due_date)
            <span class="badge {{ $task->is_overdue ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                <x-icon name="calendar" class="size-3" />
                {{ jalali_date($task->due_date) }}
                @if ($task->is_overdue)
                    — گذشته از سررسید
                @endif
            </span>
        @endif

        @if ($task->keyword)
            <a href="{{ route('admin.seo-plan.index', ['tab' => 'keywords', 'search' => $task->keyword->keyword]) }}" class="badge border-brand/30 bg-brand/10 text-brand-dark">
                {{ $task->keyword->keyword }}
            </a>
        @endif
    </div>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-2">
        <div class="flex flex-wrap items-center gap-1">
            @if ($column === 'todo')
                <form method="POST" action="{{ route('admin.seo-plan.tasks.status', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="doing">
                    <button type="submit" class="btn" data-variant="outline" data-size="sm">شروع</button>
                </form>
                <form method="POST" action="{{ route('admin.seo-plan.tasks.status', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="done">
                    <button type="submit" class="btn" data-variant="ghost" data-size="sm">انجام شد</button>
                </form>
            @elseif ($column === 'doing')
                <form method="POST" action="{{ route('admin.seo-plan.tasks.status', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="done">
                    <button type="submit" class="btn" data-variant="outline" data-size="sm">انجام شد</button>
                </form>
                <form method="POST" action="{{ route('admin.seo-plan.tasks.status', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="todo">
                    <button type="submit" class="btn" data-variant="ghost" data-size="sm">بازگشت</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.seo-plan.tasks.status', $task) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="doing">
                    <button type="submit" class="btn" data-variant="ghost" data-size="sm">بازگردانی</button>
                </form>
            @endif
        </div>

        <div class="flex items-center gap-1">
            <button
                type="button"
                class="btn"
                data-variant="outline"
                data-size="icon-sm"
                data-edit-dialog="task-edit-dialog"
                data-action="{{ route('admin.seo-plan.tasks.update', $task) }}"
                data-record="{{ json_encode($editRecord, JSON_UNESCAPED_UNICODE) }}"
                aria-label="ویرایش تسک"
                title="ویرایش تسک"
            >
                <x-icon name="edit" class="size-3.5" />
            </button>

            <form method="POST" action="{{ route('admin.seo-plan.tasks.destroy', $task) }}" data-confirm="تسک «{{ $task->title }}» حذف شود؟">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" data-variant="destructive" data-size="icon-sm" aria-label="حذف تسک" title="حذف تسک">
                    <x-icon name="trash" class="size-3.5" />
                </button>
            </form>
        </div>
    </div>
</article>
