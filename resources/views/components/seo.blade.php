@props([
    'seo' => [],
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
])

@php
    $siteName = setting('site_name', config('site.defaults.site_name'));
    $metaTitle = $title ?: ($seo['title'] ?? null) ?: setting('meta_title', $siteName);
    $metaDescription = $description ?: ($seo['description'] ?? null) ?: setting('meta_description', '');
    $canonicalUrl = $canonical ?: ($seo['canonical'] ?? null) ?: url()->current();
    $ogTitle = $seo['og_title'] ?? $metaTitle;
    $ogDescription = $seo['og_description'] ?? $metaDescription;
    $ogImagePath = $image ?: ($seo['og_image'] ?? null);

    if ($ogImagePath) {
        $ogImage = str_starts_with($ogImagePath, 'images/')
            ? asset($ogImagePath)
            : \Illuminate\Support\Facades\Storage::disk('public')->url($ogImagePath);
    } else {
        $ogImage = asset('images/hero-factory.svg');
    }

    $ogType = $type ?: ($seo['type'] ?? 'website');
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta name="robots" content="index, follow">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="fa_IR">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

@if ($ogType === 'article' && ! empty($seo['published_at']))
    <meta property="article:published_time" content="{{ $seo['published_at'] }}">
@endif
