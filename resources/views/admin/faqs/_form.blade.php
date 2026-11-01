@php
    $isEdit = $faq->exists;
    $action = $isEdit ? route('admin.faqs.update', $faq) : route('admin.faqs.store');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-5 lg:grid-cols-12">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-8">
            <div class="space-y-4">
                <x-admin.field
                    name="question"
                    label="سوال"
                    :value="$faq->question"
                    required
                />

                <x-admin.textarea
                    name="answer"
                    label="پاسخ"
                    :value="$faq->answer"
                    :rows="7"
                    required
                />
            </div>

            <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
                <button type="submit" class="btn-brand !rounded-lg !py-2.5">
                    <x-icon name="save" class="size-4" />
                    {{ $isEdit ? 'ذخیره‌ی تغییرات' : 'افزودن سوال' }}
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn" data-variant="ghost">انصراف</a>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">ترتیب نمایش</h2>
                <p class="mt-1 text-[11px] text-slate-400">عدد کوچک‌تر، بالاتر نمایش داده می‌شود.</p>

                <div class="mt-4">
                    <x-admin.field
                        name="sort_order"
                        label="ترتیب"
                        type="number"
                        :value="$faq->sort_order"
                        min="0"
                        max="9999"
                    />
                </div>
            </div>
        </div>
    </div>
</form>
