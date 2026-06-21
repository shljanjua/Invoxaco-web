<?php

use App\Controllers\Admin\AnalyticsController;
use App\Controllers\Admin\BlogController as AdminBlogController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\ContactController as AdminContactController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\PaymentController;
use App\Controllers\Admin\SeoController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\SmtpController;
use App\Controllers\Admin\SubscriptionController;
use App\Controllers\Admin\SupportController as AdminSupportController;
use App\Controllers\Admin\TemplateController;
use App\Controllers\Admin\UserController;
use App\Middleware\AdminMiddleware;

/** @var \App\Core\Router $router */

$router->group([AdminMiddleware::class], function ($router) {
    $router->get('/admin/dashboard', [AdminDashboardController::class, 'index']);

    $router->get('/admin/users', [UserController::class, 'index']);
    $router->get('/admin/users/{id}/edit', [UserController::class, 'edit']);
    $router->post('/admin/users/{id}', [UserController::class, 'update']);
    $router->post('/admin/users/{id}/delete', [UserController::class, 'destroy']);

    $router->get('/admin/subscriptions', [SubscriptionController::class, 'index']);
    $router->get('/admin/payments', [PaymentController::class, 'index']);

    $router->get('/admin/categories', [CategoryController::class, 'index']);
    $router->post('/admin/categories', [CategoryController::class, 'store']);
    $router->post('/admin/categories/{id}', [CategoryController::class, 'update']);
    $router->post('/admin/categories/{id}/delete', [CategoryController::class, 'destroy']);

    $router->get('/admin/generators', [TemplateController::class, 'index']);
    $router->get('/admin/generators/create', [TemplateController::class, 'create']);
    $router->post('/admin/generators', [TemplateController::class, 'store']);
    $router->get('/admin/generators/{id}/edit', [TemplateController::class, 'edit']);
    $router->post('/admin/generators/{id}', [TemplateController::class, 'update']);
    $router->post('/admin/generators/{id}/delete', [TemplateController::class, 'destroy']);

    $router->get('/admin/blog', [AdminBlogController::class, 'index']);
    $router->get('/admin/blog/create', [AdminBlogController::class, 'create']);
    $router->post('/admin/blog', [AdminBlogController::class, 'store']);
    $router->get('/admin/blog/{id}/edit', [AdminBlogController::class, 'edit']);
    $router->post('/admin/blog/{id}', [AdminBlogController::class, 'update']);
    $router->post('/admin/blog/{id}/delete', [AdminBlogController::class, 'destroy']);

    $router->get('/admin/contact-messages', [AdminContactController::class, 'index']);
    $router->get('/admin/contact-messages/{id}', [AdminContactController::class, 'show']);
    $router->post('/admin/contact-messages/{id}/status', [AdminContactController::class, 'updateStatus']);

    $router->get('/admin/support-tickets', [AdminSupportController::class, 'index']);
    $router->get('/admin/support-tickets/{id}', [AdminSupportController::class, 'show']);
    $router->post('/admin/support-tickets/{id}/reply', [AdminSupportController::class, 'reply']);
    $router->post('/admin/support-tickets/{id}/status', [AdminSupportController::class, 'updateStatus']);

    $router->get('/admin/seo', [SeoController::class, 'index']);
    $router->post('/admin/seo/{pageKey}', [SeoController::class, 'update']);

    $router->get('/admin/smtp', [SmtpController::class, 'index']);
    $router->post('/admin/smtp', [SmtpController::class, 'update']);
    $router->post('/admin/smtp/test', [SmtpController::class, 'test']);

    $router->get('/admin/settings', [SettingController::class, 'index']);
    $router->post('/admin/settings', [SettingController::class, 'update']);

    $router->get('/admin/analytics', [AnalyticsController::class, 'index']);
});
