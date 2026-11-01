<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b2949">
    <meta name="robots" content="noindex, nofollow">

    <title>ورود به پنل مدیریت | {{ setting('site_name', 'پلاستو ماشین') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-navy-950 text-slate-700 antialiased">
    <div class="hero-glow flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-center">
                <x-logo variant="light" />
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
                <div class="flex items-start gap-3">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand/15 text-brand-dark">
                        <x-icon name="shield" class="size-6" />
                    </span>
                    <div>
                        <h1 class="text-lg font-extrabold text-navy">ورود به پنل مدیریت</h1>
                        <p class="mt-1 text-xs leading-6 text-slate-500">با حساب مدیر وارد شوید. امکان ثبت‌نام عمومی وجود ندارد.</p>
                    </div>
                </div>

                <x-admin.flash />

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-5 space-y-4">
                    @csrf

                    <x-admin.field
                        name="email"
                        label="ایمیل مدیر"
                        type="email"
                        dir="ltr"
                        autocomplete="username"
                        placeholder="admin@plastomachin.ir"
                        required
                    />

                    <x-admin.field
                        name="password"
                        label="رمز عبور"
                        type="password"
                        dir="ltr"
                        autocomplete="current-password"
                        required
                    />

                    <div class="field">
                        <label for="remember" class="!w-full cursor-pointer">
                            <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                            <span class="text-sm text-slate-600">مرا به خاطر بسپار</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-brand !w-full !rounded-lg !py-2.5">
                        <x-icon name="logout" class="size-4 rotate-180" />
                        ورود به پنل
                    </button>
                </form>
            </div>

            <div class="mt-5 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white/60 transition hover:text-brand">
                    <x-icon name="arrow-right" class="size-4" />
                    بازگشت به سایت
                </a>
            </div>
        </div>
    </div>
</body>
</html>
