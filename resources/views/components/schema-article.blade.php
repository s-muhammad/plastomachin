@props(['post'])

@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->seo_description ?: $post->excerpt,
        'image' => $post->image_url,
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at?->toIso8601String(),
        'author' => [
            '@type' => 'Organization',
            'name' => setting('site_name', 'پلاستو ماشین'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => setting('site_name', 'پلاستو ماشین'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('favicon.svg'),
            ],
        ],
        'mainEntityOfPage' => url()->current(),
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
