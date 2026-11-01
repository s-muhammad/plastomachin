@if (session('success'))
    <div class="alert mb-5 border-emerald-200 bg-emerald-50 text-emerald-800" role="status">
        <x-icon name="check" class="size-5" />
        <h3>انجام شد</h3>
        <section>{{ session('success') }}</section>
    </div>
@endif

@if (session('error'))
    <div class="alert mb-5 border-red-200 bg-red-50 text-red-700" data-variant="destructive" role="alert">
        <x-icon name="shield" class="size-5" />
        <h3>خطا</h3>
        <section>{{ session('error') }}</section>
    </div>
@endif

@if ($errors->any())
    <div class="alert mb-5 border-red-200 bg-red-50 text-red-700" data-variant="destructive" role="alert">
        <x-icon name="shield" class="size-5" />
        <h3>لطفاً موارد مشخص‌شده را بررسی کنید</h3>
        <section>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    </div>
@endif
