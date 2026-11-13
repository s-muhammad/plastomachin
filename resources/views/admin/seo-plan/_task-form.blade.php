@php
    $task = $task ?? new \App\Models\SeoTask([
        'category' => 'content',
        'priority' => 2,
        'status' => 'todo',
    ]);
    $prefix = $prefix ?? 'task-create';
    $mode = $mode ?? 'create';
    $action = $action ?? route('admin.seo-plan.tasks.store');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    <input type="hidden" name="_form" value="{{ $prefix }}">
    @if ($mode === 'edit')
        <input type="hidden" name="_edit_id" value="{{ old('_edit_id') }}">
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-admin.field
                :id="$prefix.'-title'"
                name="title"
                label="عنوان تسک"
                :value="$task->title"
                maxlength="255"
                required
            />
        </div>

        <div class="sm:col-span-2">
            <x-admin.textarea
                :id="$prefix.'-description'"
                name="description"
                label="توضیح"
                :value="$task->description"
                :rows="4"
            />
        </div>

        <div class="field min-w-0" @if ($errors->has('category')) data-invalid @endif>
            <label for="{{ $prefix }}-category">دسته <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-category" name="category" class="max-w-full min-w-0" required>
                @foreach ($options['categories'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('category', $task->category) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field min-w-0" @if ($errors->has('priority')) data-invalid @endif>
            <label for="{{ $prefix }}-priority">اولویت <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-priority" name="priority" class="max-w-full min-w-0" required>
                @foreach ($options['priorities'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('priority', $task->priority) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('priority')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field min-w-0" @if ($errors->has('status')) data-invalid @endif>
            <label for="{{ $prefix }}-status">وضعیت <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-status" name="status" class="max-w-full min-w-0" required>
                @foreach ($options['task_statuses'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('status', $task->status) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field min-w-0" @if ($errors->has('keyword_id')) data-invalid @endif>
            <label for="{{ $prefix }}-keyword">کلمه مرتبط (اختیاری)</label>
            <select id="{{ $prefix }}-keyword" name="keyword_id" class="max-w-full min-w-0">
                <option value="">— بدون ارتباط —</option>
                @foreach ($keywordOptions as $option)
                    <option value="{{ $option->id }}" @selected((string) old('keyword_id', $task->keyword_id) === (string) $option->id)>{{ $option->keyword }}</option>
                @endforeach
            </select>
            @error('keyword_id')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <x-admin.field
            :id="$prefix.'-due-date'"
            name="due_date"
            label="تاریخ سررسید"
            type="date"
            :value="$task->due_date?->format('Y-m-d')"
            dir="ltr"
            help="تاریخ میلادی وارد می‌شود؛ در فهرست به شمسی نمایش داده می‌شود."
        />
    </div>

    <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
        <button type="submit" class="btn-brand !rounded-lg !py-2.5">
            <x-icon name="save" class="size-4" />
            {{ $mode === 'edit' ? 'ذخیره‌ی تغییرات' : 'افزودن تسک' }}
        </button>
        <button type="button" class="btn" data-variant="ghost" onclick="this.closest('dialog').close()">انصراف</button>
    </div>
</form>
