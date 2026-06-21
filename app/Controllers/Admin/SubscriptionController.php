<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index(): void
    {
        $this->view('admin/subscriptions/index', [
            'pageTitle' => 'Subscriptions',
            'subscriptions' => Subscription::allWithUser(200),
        ], 'layouts/admin');
    }
}
