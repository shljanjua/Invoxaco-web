<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): void
    {
        $this->view('admin/payments/index', [
            'pageTitle' => 'Payments',
            'payments' => Payment::recent(200),
            'totalRevenue' => Payment::totalRevenue(),
        ], 'layouts/admin');
    }
}
