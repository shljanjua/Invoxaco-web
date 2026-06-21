<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\DocumentTemplate;
use App\Services\SeoService;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'metaTitle' => 'Invoxaco - Free Online Business Document Generator',
            'metaDescription' => 'Create invoices, quotations, contracts, receipts, and 100+ business documents online. Generate, save, and download as PDF or Word in minutes. Free to start.',
            'categories' => Category::withTemplateCounts(),
            'builtTemplates' => DocumentTemplate::builtTemplates(),
            'posts' => BlogPost::published(3),
            'jsonLd' => [SeoService::softwareApplicationSchema()],
        ]);
    }
}
