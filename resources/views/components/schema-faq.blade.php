@props(['faqs'])

@php
    $items = collect($faqs)->map(function ($faq) {
        $question = is_array($faq) ? ($faq['question'] ?? '') : $faq->question;
        $answer = is_array($faq) ? ($faq['answer'] ?? '') : $faq->answer;

        return [
            '@type' => 'Question',
            'name' => strip_tags($question),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => trim(strip_tags(html_entity_decode((string) $answer))),
            ],
        ];
    })->filter(fn ($item) => $item['name'] !== '' && $item['acceptedAnswer']['text'] !== '')->values();

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items->all(),
    ];
@endphp

@if ($items->isNotEmpty())
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
