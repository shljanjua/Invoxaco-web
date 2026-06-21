<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'pageTitle' => 'Dashboard',
            'totalUsers' => User::count(),
            'totalDocuments' => Document::count(),
            'totalRevenue' => Payment::totalRevenue(),
            'activeSubscriptions' => Subscription::count(['status' => 'active']),
            'openTickets' => SupportTicket::count(['status' => 'open']),
            'newMessages' => ContactMessage::count(['status' => 'new']),
            'recentUsers' => User::paginateAll(1, 5),
            'recentPayments' => Payment::recent(8),
            'recentActivity' => ActivityLog::recent(10),
        ], 'layouts/admin');
    }
}
