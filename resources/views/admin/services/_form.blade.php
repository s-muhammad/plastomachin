@php
    $isEdit = $service->exists;
    $action = $isEdit ? route('admin.services.update', $service) : route('admin.services.store');

    $faqRows = old('faqs', $isEdit
        ? $service->faqs->map(fn ($faq) => ['question' => $faq->question, 'answer' => $faq->answer])->all()
        : []);

    $nextFaqIndex = $faqRows === [] ? 0 : max(array_map('intval', array_keys($faqRows))) + 1;
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-5 lg:grid-cols-12">
        <div class="space-y-5 lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5 border-b border-slate-100 pb-4">
                    <h2 class="text-sm font-extrabold text-navy">اطلاعات اصلی</h2>
                    <p class="mt-1 text-[11px] text-slate-400">عنوان و متن این صفحه در سایت و نتایج جستجو دیده می‌شود.</p>
                </div>

                <div class="space-y-4">
                    <x-admin.field
                        name="title"
                        label="عنوان صفحه"
                        :value="$service->title"
                        required
                        help="در فهرست حوزه‌های کاری و منوهای سایت استفاده می‌شود."
                    />

                    <x-admin.field
                        name="slug"
                        label="آدرس صفحه (slug)"
                        :value="$service->slug"
                        dir="ltr"
                        help="اگر خالی بماند از عنوان ساخته می‌شود. حروف فارسی مجاز است."
                    />

                    <x-admin.field
                        name="h1"
                        label="تیتر اصلی (H1)"
                        :value="$service->h1"
                        required
                        help="تیتر اصلی صفحه؛ بهتر است موضوع و شهر در آن باشد."
                    />

                    <x-admin.textarea
                        name="excerpt"
                        label="خلاصه"
                        :value="$service->excerpt"
                        :rows="3"
                        help="در کارت‌های سایت و توضیح کوتاه صفحه نمایش داده می‌شود."
                    />

                    <x-admin.textarea
                        name="body"
                        label="متن صفحه"
                        :value="$service->body"
                        :rows="16"
                        editor
                        help="متن را با ویرایشگر بنویسید. برای درج تصویر از دکمه‌ی تصویر و آدرس تصویر استفاده کنید."
                    />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-faq-repeater data-next-index="{{ $nextFaqIndex }}">
                <div class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-navy">سوالات متداول این صفحه</h2>
                        <p class="mt-1 text-[11px] text-slate-400">در انتهای صفحه و در داده‌های ساختاریافته‌ی FAQ نمایش داده می‌شود.</p>
                    </div>

                    <button type="button" class="btn" data-variant="outline" data-size="sm" data-faq-add>
                        <x-icon name="plus" class="size-3.5" />
                        افزودن سوال
                    </button>
                </div>

                <div class="mt-4 space-y-3" data-faq-rows>
                    @foreach ($faqRows as $index => $faq)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4" data-faq-row>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[11px] font-bold text-slate-500">سوال {{ fa_num($index + 1) }}</span>
                                <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" data-faq-remove aria-label="حذف سوال">
                                    <x-icon name="trash" class="size-3.5" />
                                </button>
                            </div>

                            <div class="mt-3 space-y-3">
                                <div class="field">
                                    <label for="faq-question-{{ $index }}">سوال</label>
                                    <input id="faq-question-{{ $index }}" name="faqs[{{ $index }}][question]" type="text" value="{{ $faq['question'] ?? '' }}">
                                </div>

                                <div class="field">
                                    <label for="faq-answer-{{ $index }}">پاسخ</label>
                                    <textarea id="faq-answer-{{ $index }}" name="faqs[{{ $index }}][answer]" rows="3">{{ $faq['answer'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($faqRows === [])
                    <p class="mt-4 rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-xs text-slate-400" data-faq-empty>
                        هنوز سوالی اضافه نشده است. با دکمه‌ی «افزودن سوال» شروع کنید.
                    </p>
                @endif

                <template>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4" data-faq-row>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[11px] font-bold text-slate-500">سوال جدید</span>
                            <button type="button" class="btn" data-variant="ghost" data-size="icon-sm" data-faq-remove aria-label="حذف سوال">
                                <x-icon name="trash" class="size-3.5" />
                            </button>
                        </div>

                        <div class="mt-3 space-y-3">
                            <div class="field">
                                <label for="faq-question-__INDEX__">سوال</label>
                                <input id="faq-question-__INDEX__" name="faqs[__INDEX__][question]" type="text">
                            </div>

                            <div class="field">
                                <label for="faq-answer-__INDEX__">پاسخ</label>
                                <textarea id="faq-answer-__INDEX__" name="faqs[__INDEX__][answer]" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="space-y-5 lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">انتشار</h2>

                <div class="mt-4 space-y-4">
                    <x-admin.checkbox
                        name="is_active"
                        label="نمایش این صفحه در سایت"
                        :checked="$service->is_active"
                        help="در صورت خاموش بودن، صفحه از سایت و نقشه‌ی سایت حذف می‌شود."
                    />

                    <x-admin.field
                        name="sort_order"
                        label="ترتیب نمایش"
                        type="number"
                        :value="$service->sort_order"
                        min="0"
                        max="9999"
                        help="عدد کوچک‌تر، بالاتر نمایش داده می‌شود."
                    />
                </div>

                <div class="mt-5 flex flex-col gap-2 border-t border-slate-100 pt-4">
                    <button type="submit" class="btn-brand !rounded-lg !py-2.5">
                        <x-icon name="save" class="size-4" />
                        {{ $isEdit ? 'ذخیره‌ی تغییرات' : 'ایجاد صفحه' }}
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn !w-full" data-variant="ghost">انصراف</a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">تصویر شاخص</h2>
                <p class="mt-1 text-[11px] text-slate-400">JPG، PNG یا WebP تا ۴ مگابایت.</p>

                @if ($service->image)
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="480" height="300" class="mt-3 aspect-[16/10] w-full rounded-xl border border-slate-200 object-cover">
                @else
                    <div class="mt-3 flex aspect-[16/10] items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-400">
                        <x-icon name="image" class="size-8" />
                    </div>
                @endif

                <div class="mt-3">
                    <x-admin.field
                        name="image"
                        label="انتخاب تصویر"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        help="در صورت انتخاب، تصویر فعلی جایگزین می‌شود."
                    />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">تنظیمات سئو</h2>
                <p class="mt-1 text-[11px] text-slate-400">در صورت خالی بودن، عنوان و خلاصه‌ی صفحه استفاده می‌شود.</p>

                <div class="mt-4 space-y-4">
                    <x-admin.field name="seo_title" label="عنوان متا" :value="$service->seo_title" maxlength="255" />
                    <x-admin.textarea name="seo_description" label="توضیح متا" :value="$service->seo_description" :rows="4" maxlength="500" />
                    <x-admin.field name="canonical" label="Canonical" :value="$service->canonical" dir="ltr" placeholder="https://plastomachin.ir/services/..." />
                </div>
            </div>
        </div>
    </div>
</form>
