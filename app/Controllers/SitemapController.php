<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\DocumentTemplate;

class SitemapController extends Controller
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (['sitemap-pages.xml', 'sitemap-generators.xml', 'sitemap-blog.xml'] as $file) {
            echo '<sitemap><loc>' . e(url($file)) . '</loc></sitemap>' . "\n";
        }
        echo '</sitemapindex>';
    }

    public function pages(): void
    {
        $urls = ['/', 'features', 'pricing', 'templates', 'generators', 'about', 'contact', 'faq', 'help', 'blog',
            'legal/privacy-policy', 'legal/terms-of-service', 'legal/refund-policy', 'legal/cookie-policy',
            'legal/disclaimer', 'legal/acceptable-use-policy', 'legal/data-processing-policy',
            'legal/gdpr-compliance', 'legal/ccpa-compliance'];

        $this->renderUrlset(array_map(fn ($p) => url($p), $urls));
    }

    public function generators(): void
    {
        $urls = array_map(
            fn ($t) => url('generators/' . $t['slug']),
            DocumentTemplate::where(['is_active' => 1])
        );

        $this->renderUrlset($urls);
    }

    public function blog(): void
    {
        $urls = array_map(
            fn ($p) => url('blog/' . $p['slug']),
            BlogPost::published()
        );

        $this->renderUrlset($urls);
    }

    private function renderUrlset(array $urls): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo '<url><loc>' . e($url) . '</loc></url>' . "\n";
        }
        echo '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /dashboard\n";
        echo "Disallow: /documents\n";
        echo "Disallow: /clients\n";
        echo "Disallow: /team\n";
        echo "Disallow: /support\n";
        echo "Disallow: /admin\n";
        echo "Sitemap: " . url('sitemap.xml') . "\n";
    }
}
