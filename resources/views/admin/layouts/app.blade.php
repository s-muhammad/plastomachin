<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b2949">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'داشبورد') | {{ setting('site_name', 'پلاستو ماشین') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-700 antialiased">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="flex min-h-screen flex-col">
            @include('admin.partials.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <x-admin.flash />
                @yield('content')
            </main>

            <footer class="px-4 pb-6 text-center text-[11px] text-slate-400">
                پنل مدیریت {{ setting('site_name', 'پلاستو ماشین') }} — {{ jalali_date(now()) }}
            </footer>
        </div>
    </div>
</body>
</html>
