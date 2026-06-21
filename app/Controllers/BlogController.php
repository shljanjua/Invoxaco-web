<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\SeoService;

class BlogController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $perPage = 9;

        $this->view('blog/index', [
            'metaTitle' => 'Blog - Invoxaco',
            'metaDescription' => 'Tips and guides on invoicing, contracts, freelancing, and running a small business, from the Invoxaco team.',
            'posts' => BlogPost::published($perPage, ($page - 1) * $perPage),
            'categories' => BlogCategory::all('name', 'ASC'),
            'page' => $page,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Blog', 'url' => url('blog')],
            ])],
        ]);
    }

    public function category(string $slug): void
    {
        $category = BlogCategory::findBy('slug', $slug);

        if (!$category) {
            Response::abort(404, 'Category not found');
        }

        $posts = array_filter(BlogPost::published(), fn ($p) => $p['category_slug'] === $slug);

        $this->view('blog/category', [
            'metaTitle' => $category['name'] . ' - Invoxaco Blog',
            'metaDescription' => 'Articles in the ' . $category['name'] . ' category on the Invoxaco blog.',
            'category' => $category,
            'posts' => $posts,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Blog', 'url' => url('blog')],
                ['name' => $category['name'], 'url' => url('blog/category/' . $slug)],
            ])],
        ]);
    }

    public function show(string $slug): void
    {
        $post = BlogPost::findPublishedBySlug($slug);

        if (!$post) {
            Response::abort(404, 'Post not found');
        }

        $this->view('blog/show', [
            'metaTitle' => $post['meta_title'] ?? ($post['title'] . ' - Invoxaco Blog'),
            'metaDescription' => $post['meta_description'] ?? $post['excerpt'],
            'post' => $post,
            'jsonLd' => [
                SeoService::breadcrumbSchema([
                    ['name' => 'Home', 'url' => url()],
                    ['name' => 'Blog', 'url' => url('blog')],
                    ['name' => $post['title'], 'url' => url('blog/' . $post['slug'])],
                ]),
                SeoService::articleSchema($post),
            ],
        ]);
    }
}
