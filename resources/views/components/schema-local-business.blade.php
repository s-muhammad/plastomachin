@php
    $phone = preg_replace('/\D+/', '', setting('phone', ''));
    $socials = [];

    if (setting('socials_enabled', '0') === '1') {
        foreach (['instagram', 'telegram'] as $network) {
            if (setting($network)) {
                $socials[] = setting($network);
            }
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => setting('site_name', 'پلاستو ماشین'),
        'description' => setting('meta_description', ''),
        'url' => route('home'),
        'telephone' => $phone,
        'image' => asset('images/hero-factory.svg'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => setting('address', ''),
            'addressLocality' => 'تهران',
            'addressRegion' => 'تهران',
            'addressCountry' => 'IR',
        ],
        'areaServed' => 'تهران',
        'hasMap' => setting('map_url', ''),
    ];

    if (setting('working_hours')) {
        $schema['openingHours'] = setting('working_hours');
    }

    if ($socials) {
        $schema['sameAs'] = $socials;
    }
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
