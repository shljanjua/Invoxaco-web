<?php

namespace App\Services;

class SeoService
{
    public static function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Invoxaco',
            'url' => url(),
            'logo' => asset('img/logo.png'),
            'email' => 'support@invoxaco.com',
            'sameAs' => [],
        ];
    }

    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Invoxaco',
            'url' => url(),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url() . '/generators?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function softwareApplicationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'Invoxaco',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'ratingCount' => '120',
            ],
        ];
    }

    public static function breadcrumbSchema(array $items): array
    {
        $list = [];

        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public static function faqSchema(array $faqs): array
    {
        $entities = array_map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $f['a'],
            ],
        ], $faqs);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    public static function articleSchema(array $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post['title'],
            'description' => $post['excerpt'] ?? '',
            'image' => $post['featured_image'] ? url('uploads/blog/' . $post['featured_image']) : asset('img/og-default.png'),
            'datePublished' => $post['published_at'] ?? $post['created_at'],
            'dateModified' => $post['updated_at'] ?? $post['created_at'],
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name'] ?? 'Invoxaco Team',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Invoxaco',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('img/logo.png'),
                ],
            ],
        ];
    }

    public static function render(array $schema): string
    {
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
