<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(): void
    {
        $this->view('admin/blog/index', [
            'pageTitle' => 'Blog',
            'posts' => BlogPost::allWithCategory(),
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $this->view('admin/blog/form', [
            'pageTitle' => 'New Post',
            'post' => null,
            'categories' => BlogCategory::all('name', 'ASC'),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();

        $data = $this->collect();
        $data['author_id'] = Auth::id();

        BlogPost::create($data);
        $this->flashAndRedirect('success', 'Post created.', url('admin/blog'));
    }

    public function edit(int $id): void
    {
        $post = BlogPost::find($id);

        if (!$post) {
            Response::abort(404, 'Post not found');
        }

        $this->view('admin/blog/form', [
            'pageTitle' => 'Edit Post',
            'post' => $post,
            'categories' => BlogCategory::all('name', 'ASC'),
        ], 'layouts/admin');
    }

    public function update(int $id): void
    {
        $this->validateCsrf();

        if (!BlogPost::find($id)) {
            Response::abort(404, 'Post not found');
        }

        BlogPost::update($id, $this->collect());
        $this->flashAndRedirect('success', 'Post updated.', url('admin/blog'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        BlogPost::delete($id);
        $this->flashAndRedirect('success', 'Post deleted.', url('admin/blog'));
    }

    private function collect(): array
    {
        $title = Request::string('title');
        $status = Request::string('status') === 'published' ? 'published' : 'draft';
        $categoryId = Request::input('category_id');

        return [
            'category_id' => $categoryId !== '' && $categoryId !== null ? (int) $categoryId : null,
            'title' => $title,
            'slug' => Request::string('slug') ?: slugify($title),
            'excerpt' => Request::string('excerpt') ?: null,
            'content' => Request::string('content'),
            'featured_image' => Request::string('featured_image') ?: null,
            'tags' => Request::string('tags') ?: null,
            'status' => $status,
            'meta_title' => Request::string('meta_title') ?: null,
            'meta_description' => Request::string('meta_description') ?: null,
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ];
    }
}
