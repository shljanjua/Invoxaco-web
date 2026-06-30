<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\DigitalProduct;
use App\Models\StoreCategory;
use App\Services\FileUploader;
use App\Services\ProductFileUploader;

class ProductController extends Controller
{
    private const TYPES = ['ebook', 'template', 'document', 'book', 'course', 'bundle', 'other'];

    public function index(): void
    {
        $this->view('admin/products/index', [
            'pageTitle' => 'Digital Store',
            'products' => DigitalProduct::allForAdmin(),
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $this->view('admin/products/form', [
            'pageTitle' => 'New Product',
            'product' => null,
            'categories' => StoreCategory::ordered(),
            'types' => self::TYPES,
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();

        $data = $this->collect();

        try {
            $cover = Request::file('cover_image');
            if ($cover && ($cover['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $data['cover_image'] = FileUploader::storeImage($cover, 'products');
            }

            $file = Request::file('product_file');
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $stored = ProductFileUploader::store($file);
                $data['file_path'] = $stored['file_path'];
                $data['file_name'] = $stored['file_name'];
                $data['file_size'] = $stored['file_size'];
                $data['file_mime'] = $stored['file_mime'];
            }
        } catch (\RuntimeException $e) {
            $this->flashAndRedirect('error', $e->getMessage(), url('admin/products/create'));
        }

        DigitalProduct::create($data);
        $this->flashAndRedirect('success', 'Product created.', url('admin/products'));
    }

    public function edit(int $id): void
    {
        $product = DigitalProduct::find($id);
        if (!$product) {
            Response::abort(404, 'Product not found');
        }

        $this->view('admin/products/form', [
            'pageTitle' => 'Edit Product',
            'product' => $product,
            'categories' => StoreCategory::ordered(),
            'types' => self::TYPES,
        ], 'layouts/admin');
    }

    public function update(int $id): void
    {
        $this->validateCsrf();

        $product = DigitalProduct::find($id);
        if (!$product) {
            Response::abort(404, 'Product not found');
        }

        $data = $this->collect();

        try {
            $cover = Request::file('cover_image');
            if ($cover && ($cover['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                FileUploader::delete('products', $product['cover_image']);
                $data['cover_image'] = FileUploader::storeImage($cover, 'products');
            }

            $file = Request::file('product_file');
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $stored = ProductFileUploader::store($file);
                ProductFileUploader::delete($product['file_path']);
                $data['file_path'] = $stored['file_path'];
                $data['file_name'] = $stored['file_name'];
                $data['file_size'] = $stored['file_size'];
                $data['file_mime'] = $stored['file_mime'];
            }
        } catch (\RuntimeException $e) {
            $this->flashAndRedirect('error', $e->getMessage(), url('admin/products/' . $id . '/edit'));
        }

        DigitalProduct::update($id, $data);
        $this->flashAndRedirect('success', 'Product updated.', url('admin/products'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();

        $product = DigitalProduct::find($id);
        if ($product) {
            ProductFileUploader::delete($product['file_path']);
            FileUploader::delete('products', $product['cover_image']);
            DigitalProduct::delete($id);
        }

        $this->flashAndRedirect('success', 'Product deleted.', url('admin/products'));
    }

    private function collect(): array
    {
        $name = trim(Request::string('name'));
        $type = Request::string('type');
        $price = round((float) Request::input('price', 0), 2);
        $saleRaw = Request::string('sale_price');

        $categoryId = (int) Request::input('category_id');

        return [
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'name' => $name,
            'slug' => Request::string('slug') ?: slugify($name),
            'type' => in_array($type, self::TYPES, true) ? $type : 'ebook',
            'short_description' => Request::string('short_description') ?: null,
            'description' => Request::string('description') ?: null,
            'price' => $price,
            'sale_price' => $saleRaw !== '' ? round((float) $saleRaw, 2) : null,
            'currency' => strtoupper(Request::string('currency') ?: 'USD'),
            'is_featured' => Request::input('is_featured') ? 1 : 0,
            'is_active' => Request::input('is_active') ? 1 : 0,
            'sort_order' => (int) Request::input('sort_order', 0),
            'meta_title' => Request::string('meta_title') ?: null,
            'meta_description' => Request::string('meta_description') ?: null,
        ];
    }
}
