@php
    $keyword = $keyword ?? new \App\Models\SeoKeyword([
        'role' => 'secondary',
        'page_type' => 'service',
        'intent' => 'commercial',
        'priority' => 2,
        'status' => 'planned',
    ]);
    $prefix = $prefix ?? 'keyword-create';
    $mode = $mode ?? 'create';
    $action = $action ?? route('admin.seo-plan.keywords.store');
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
                :id="$prefix.'-keyword'"
                name="keyword"
                label="کلمه یا عبارت"
                :value="$keyword->keyword"
                maxlength="191"
                required
            />
        </div>

        <div class="field" @if ($errors->has('role')) data-invalid @endif>
            <label for="{{ $prefix }}-role">نقش <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-role" name="role" required>
                @foreach ($options['roles'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('role', $keyword->role) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('role')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field" @if ($errors->has('page_type')) data-invalid @endif>
            <label for="{{ $prefix }}-page-type">نوع صفحه <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-page-type" name="page_type" required>
                @foreach ($options['page_types'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('page_type', $keyword->page_type) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('page_type')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field" @if ($errors->has('intent')) data-invalid @endif>
            <label for="{{ $prefix }}-intent">قصد جستجو <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-intent" name="intent" required>
                @foreach ($options['intents'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('intent', $keyword->intent) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('intent')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field" @if ($errors->has('priority')) data-invalid @endif>
            <label for="{{ $prefix }}-priority">اولویت <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-priority" name="priority" required>
                @foreach ($options['priorities'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('priority', $keyword->priority) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('priority')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field" @if ($errors->has('status')) data-invalid @endif>
            <label for="{{ $prefix }}-status">وضعیت <span class="text-destructive">*</span></label>
            <select id="{{ $prefix }}-status" name="status" required>
                @foreach ($options['keyword_statuses'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('status', $keyword->status) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="field" @if ($errors->has('competition')) data-invalid @endif>
            <label for="{{ $prefix }}-competition">رقابت</label>
            <select id="{{ $prefix }}-competition" name="competition">
                <option value="">—</option>
                @foreach ($options['competitions'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('competition', $keyword->competition) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('competition')<p role="alert">{{ $message }}</p>@enderror
        </div>

        <x-admin.field
            :id="$prefix.'-volume'"
            name="volume"
            label="حجم جستجوی ماهانه"
            type="number"
            :value="$keyword->volume"
            min="0"
            max="4294967295"
            dir="ltr"
        />

        <x-admin.field
            :id="$prefix.'-position'"
            name="position"
            label="رتبه فعلی"
            type="number"
            :value="$keyword->position"
            min="0"
            max="65535"
            dir="ltr"
        />

        <x-admin.field
            :id="$prefix.'-target-page'"
            name="target_page"
            label="صفحه هدف"
            :value="$keyword->target_page"
            maxlength="255"
        />

        <x-admin.field
            :id="$prefix.'-target-url'"
            name="target_url"
            label="آدرس صفحه هدف"
            :value="$keyword->target_url"
            dir="ltr"
            maxlength="255"
            placeholder="/used-nylon-machine/"
        />

        <div class="sm:col-span-2">
            <x-admin.field
                :id="$prefix.'-content-title'"
                name="content_title"
                label="عنوان پیشنهادی مقاله"
                :value="$keyword->content_title"
                maxlength="255"
                help="برای ردیف‌های مقاله وبلاگ."
            />
        </div>

        <div class="sm:col-span-2">
            <x-admin.textarea
                :id="$prefix.'-secondary-keywords'"
                name="secondary_keywords"
                label="کلمات فرعی"
                :value="$keyword->secondary_keywords"
                :rows="2"
            />
        </div>

        <div class="sm:col-span-2">
            <x-admin.textarea
                :id="$prefix.'-internal-links'"
                name="internal_links"
                label="لینک‌های داخلی"
                :value="$keyword->internal_links"
                :rows="2"
            />
        </div>

        <div class="sm:col-span-2">
            <x-admin.textarea
                :id="$prefix.'-notes'"
                name="notes"
                label="یادداشت"
                :value="$keyword->notes"
                :rows="3"
            />
        </div>
    </div>

    <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
        <button type="submit" class="btn-brand !rounded-lg !py-2.5">
            <x-icon name="save" class="size-4" />
            {{ $mode === 'edit' ? 'ذخیره‌ی تغییرات' : 'افزودن کلمه' }}
        </button>
        <button type="button" class="btn" data-variant="ghost" onclick="this.closest('dialog').close()">انصراف</button>
    </div>
</form>
