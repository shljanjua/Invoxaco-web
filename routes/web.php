<?php

use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\ClientController;
use App\Controllers\ContactController;
use App\Controllers\DashboardController;
use App\Controllers\DocumentController;
use App\Controllers\GeneratorController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\SitemapController;
use App\Controllers\SupportController;
use App\Controllers\TeamController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/** @var \App\Core\Router $router */

// Public pages
$router->get('/', [HomeController::class, 'index']);
$router->get('/features', [PageController::class, 'features']);
$router->get('/pricing', [PageController::class, 'pricing']);
$router->get('/templates', [PageController::class, 'templates']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/faq', [PageController::class, 'faq']);
$router->get('/help', [PageController::class, 'help']);
$router->get('/contact', [ContactController::class, 'show']);
$router->post('/contact', [ContactController::class, 'store']);
$router->get('/legal/{slug}', [PageController::class, 'legal']);

// Auth
$router->group([GuestMiddleware::class], function ($router) {
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/forgot-password', [AuthController::class, 'showForgot']);
    $router->post('/forgot-password', [AuthController::class, 'forgot']);
    $router->get('/reset-password/{token}', [AuthController::class, 'showReset']);
    $router->post('/reset-password', [AuthController::class, 'reset']);
});
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
$router->post('/verify-email/resend', [AuthController::class, 'resendVerification'], [AuthMiddleware::class]);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// Generators catalog
$router->get('/generators', [GeneratorController::class, 'catalog']);
$router->get('/generators/category/{slug}', [GeneratorController::class, 'category']);
$router->post('/generators/{slug}/notify', [GeneratorController::class, 'notify']);
$router->get('/generators/{slug}', [GeneratorController::class, 'show']);

// Documents
$router->group([AuthMiddleware::class], function ($router) {
    $router->get('/documents', [DocumentController::class, 'index']);
    $router->get('/documents/create/{slug}', [DocumentController::class, 'create']);
    $router->post('/documents', [DocumentController::class, 'store']);
    $router->get('/documents/{id}/edit', [DocumentController::class, 'edit']);
    $router->post('/documents/{id}', [DocumentController::class, 'update']);
    $router->post('/documents/{id}/autosave', [DocumentController::class, 'autosave']);
    $router->post('/documents/{id}/duplicate', [DocumentController::class, 'duplicate']);
    $router->post('/documents/{id}/delete', [DocumentController::class, 'destroy']);
    $router->get('/documents/{id}/pdf', [DocumentController::class, 'pdf']);
    $router->get('/documents/{id}/docx', [DocumentController::class, 'docx']);
    $router->get('/documents/{id}/print', [DocumentController::class, 'print']);
    $router->post('/documents/{id}/email', [DocumentController::class, 'email']);
    $router->post('/documents/{id}/share', [DocumentController::class, 'enableShare']);
});
$router->get('/share/{token}', [DocumentController::class, 'shared']);
$router->get('/share/{token}/pdf', [DocumentController::class, 'sharedPdf']);

// Clients
$router->group([AuthMiddleware::class], function ($router) {
    $router->get('/clients', [ClientController::class, 'index']);
    $router->get('/clients/create', [ClientController::class, 'create']);
    $router->post('/clients', [ClientController::class, 'store']);
    $router->get('/clients/{id}/edit', [ClientController::class, 'edit']);
    $router->post('/clients/{id}', [ClientController::class, 'update']);
    $router->post('/clients/{id}/delete', [ClientController::class, 'destroy']);
    $router->get('/clients/{id}', [ClientController::class, 'show']);
});

// Team
$router->group([AuthMiddleware::class], function ($router) {
    $router->get('/team', [TeamController::class, 'index']);
    $router->post('/team/invite', [TeamController::class, 'invite']);
    $router->post('/team/{id}/remove', [TeamController::class, 'remove']);
});
$router->get('/team/accept/{token}', [TeamController::class, 'accept'], [AuthMiddleware::class]);

// Blog
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/category/{slug}', [BlogController::class, 'category']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

// Support tickets
$router->group([AuthMiddleware::class], function ($router) {
    $router->get('/support', [SupportController::class, 'index']);
    $router->get('/support/create', [SupportController::class, 'create']);
    $router->post('/support', [SupportController::class, 'store']);
    $router->get('/support/{id}', [SupportController::class, 'show']);
    $router->post('/support/{id}/reply', [SupportController::class, 'reply']);
});

// SEO infra
$router->get('/sitemap.xml', [SitemapController::class, 'index']);
$router->get('/sitemap-pages.xml', [SitemapController::class, 'pages']);
$router->get('/sitemap-generators.xml', [SitemapController::class, 'generators']);
$router->get('/sitemap-blog.xml', [SitemapController::class, 'blog']);
$router->get('/robots.txt', [SitemapController::class, 'robots']);
