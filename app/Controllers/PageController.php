<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Category;
use App\Models\DocumentTemplate;
use App\Services\SeoService;

class PageController extends Controller
{
    public function features(): void
    {
        $this->view('pages/features', [
            'metaTitle' => 'Features - Invoxaco',
            'metaDescription' => 'Explore Invoxaco features: 110+ document generators, PDF & DOCX export, autosave, email delivery, shareable links, client management, team collaboration, and more.',
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Features', 'url' => url('features')],
            ])],
        ]);
    }

    public function pricing(): void
    {
        $this->view('pages/pricing', [
            'metaTitle' => 'Pricing - Invoxaco',
            'metaDescription' => 'Simple, transparent pricing. Start free with 10 documents a month, or upgrade to Pro or Premium for unlimited documents, premium templates, and team features.',
            'plans' => config('plans'),
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Pricing', 'url' => url('pricing')],
            ])],
        ]);
    }

    public function templates(): void
    {
        $this->view('pages/templates', [
            'metaTitle' => 'Templates - Invoxaco',
            'metaDescription' => 'Browse every Invoxaco document template across Financial, Sales, Legal, HR, Construction, Real Estate, Freelancer, Operations, Marketing, and Startup categories.',
            'categories' => Category::withTemplateCounts(),
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Templates', 'url' => url('templates')],
            ])],
        ]);
    }

    public function about(): void
    {
        $this->view('pages/about', [
            'metaTitle' => 'About Us - Invoxaco',
            'metaDescription' => 'Invoxaco helps freelancers, small businesses, and teams create professional invoices, contracts, and business documents in minutes.',
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'About', 'url' => url('about')],
            ])],
        ]);
    }

    public function faq(): void
    {
        $faqs = [
            ['q' => 'Is Invoxaco free to use?', 'a' => 'Yes. The Free plan lets you create up to 10 documents a month using basic templates, with no credit card required.'],
            ['q' => 'What document formats can I download?', 'a' => 'You can download documents as PDF on every plan. DOCX (Word) export is available on Pro and Premium plans.'],
            ['q' => 'Can I email documents directly to clients?', 'a' => 'Yes, on Pro and Premium plans you can email a PDF or DOCX copy of any document directly from the editor.'],
            ['q' => 'Do free documents have a watermark?', 'a' => 'Documents created on the Free plan include a small Invoxaco watermark. Upgrading to Pro or Premium removes it.'],
            ['q' => 'Can I cancel anytime?', 'a' => 'Yes, you can cancel your subscription anytime from your dashboard with no cancellation fee.'],
            ['q' => 'Is my data secure?', 'a' => 'Yes. All connections are encrypted, passwords are hashed, and your documents are only accessible to you unless you create a shareable link.'],
        ];

        $this->view('pages/faq', [
            'metaTitle' => 'Frequently Asked Questions - Invoxaco',
            'metaDescription' => 'Answers to common questions about Invoxaco pricing, document formats, exports, security, and account management.',
            'faqs' => $faqs,
            'jsonLd' => [
                SeoService::breadcrumbSchema([
                    ['name' => 'Home', 'url' => url()],
                    ['name' => 'FAQ', 'url' => url('faq')],
                ]),
                SeoService::faqSchema($faqs),
            ],
        ]);
    }

    public function help(): void
    {
        $this->view('pages/help', [
            'metaTitle' => 'Help Center - Invoxaco',
            'metaDescription' => 'Find guides and answers for getting started with Invoxaco, creating documents, managing your subscription, and contacting support.',
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Help Center', 'url' => url('help')],
            ])],
        ]);
    }

    public function legal(string $slug): void
    {
        $pages = config('legal');
        $page = $pages[$slug] ?? null;

        if (!$page) {
            Response::abort(404, 'Page not found');
        }

        $this->view('pages/legal', [
            'metaTitle' => $page['title'] . ' - Invoxaco',
            'metaDescription' => 'Read the Invoxaco ' . $page['title'] . '.',
            'page' => $page,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Legal', 'url' => url('legal/privacy-policy')],
                ['name' => $page['title'], 'url' => url('legal/' . $slug)],
            ])],
        ]);
    }
}
