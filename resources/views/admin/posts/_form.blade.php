@php
    $isEdit = $post->exists;
    $action = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
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
                    <h2 class="text-sm font-extrabold text-navy">محتوای مقاله</h2>
                    <p class="mt-1 text-[11px] text-slate-400">عنوان و متن اصلی مقاله.</p>
                </div>

                <div class="space-y-4">
                    <x-admin.field
                        name="title"
                        label="عنوان مقاله"
                        :value="$post->title"
                        required
                        help="در فهرست مقالات و نتایج جستجو نمایش داده می‌شود."
                    />

                    <x-admin.field
                        name="slug"
                        label="آدرس مقاله (slug)"
                        :value="$post->slug"
                        dir="ltr"
                        help="اگر خالی بماند از عنوان ساخته می‌شود."
                    />

                    <x-admin.textarea
                        name="excerpt"
                        label="خلاصه"
                        :value="$post->excerpt"
                        :rows="3"
                        maxlength="500"
                        help="در کارت مقاله و توضیح متا استفاده می‌شود."
                    />

                    <x-admin.textarea
                        name="body"
                        label="متن مقاله (HTML)"
                        :value="$post->body"
                        :rows="18"
                        dir="ltr"
                        class="min-h-80 font-mono !text-xs leading-6"
                        help="تگ‌های مجاز: p، h2، h3، ul، ol، li، strong، a و blockquote."
                    />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-sm font-extrabold text-navy">تنظیمات سئو</h2>
                <p class="mt-1 text-[11px] text-slate-400">در صورت خالی بودن، عنوان و خلاصه‌ی مقاله استفاده می‌شود.</p>

                <div class="mt-4 space-y-4">
                    <x-admin.field name="seo_title" label="عنوان متا" :value="$post->seo_title" maxlength="255" />
                    <x-admin.textarea name="seo_description" label="توضیح متا" :value="$post->seo_description" :rows="4" maxlength="500" />
                    <x-admin.field name="canonical" label="Canonical" :value="$post->canonical" dir="ltr" placeholder="https://plastomachin.ir/blog/..." />
                </div>
            </div>
        </div>

        <div class="space-y-5 lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">انتشار</h2>

                <div class="mt-4 space-y-4">
                    <div class="field" @if ($errors->has('status')) data-invalid @endif>
                        <label for="status">وضعیت انتشار <span class="text-destructive">*</span></label>
                        <select id="status" name="status" class="w-full" @if ($errors->has('status')) aria-invalid="true" aria-describedby="status-error" @endif>
                            <option value="draft" @selected(old('status', $post->status) === 'draft')>پیش‌نویس</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>منتشرشده</option>
                        </select>
                        @error('status')
                            <p id="status-error" role="alert">{{ $message }}</p>
                        @else
                            <p>پیش‌نویس در سایت نمایش داده نمی‌شود.</p>
                        @enderror
                    </div>

                    <x-admin.field
                        name="published_at"
                        label="تاریخ و ساعت انتشار"
                        type="datetime-local"
                        :value="$post->published_at?->format('Y-m-d\TH:i')"
                        dir="ltr"
                        help="خالی بماند، زمان ذخیره استفاده می‌شود. برای انتشار آینده زمان دلخواه را وارد کنید."
                    />
                </div>

                <div class="mt-5 flex flex-col gap-2 border-t border-slate-100 pt-4">
                    <button type="submit" class="btn-brand !rounded-lg !py-2.5">
                        <x-icon name="save" class="size-4" />
                        {{ $isEdit ? 'ذخیره‌ی تغییرات' : 'ایجاد مقاله' }}
                    </button>
                    <a href="{{ route('admin.posts.index') }}" class="btn !w-full" data-variant="ghost">انصراف</a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-navy">تصویر شاخص</h2>
                <p class="mt-1 text-[11px] text-slate-400">JPG، PNG یا WebP تا ۴ مگابایت.</p>

                @if ($post->image)
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" width="480" height="300" class="mt-3 aspect-[16/10] w-full rounded-xl border border-slate-200 object-cover">
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
        </div>
    </div>
</form>
