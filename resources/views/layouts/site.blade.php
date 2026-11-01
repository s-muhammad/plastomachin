<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b2949">

    <x-seo :seo="$seo ?? []" :title="$title ?? null" :description="$description ?? null" :canonical="$canonical ?? null" :image="$image ?? null" />

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-700 antialiased">
    <a href="#main" class="sr-only z-50 rounded-md bg-brand px-4 py-2 text-navy focus:not-sr-only focus:absolute focus:top-2 focus:start-2">رفتن به محتوای اصلی</a>

    <x-site.header />

    <main id="main">
        @yield('content')
    </main>

    <x-site.footer />
    <x-site.mobile-bar />

    @stack('scripts')
</body>
</html>
