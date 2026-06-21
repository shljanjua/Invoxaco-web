<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Throwable;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'pageTitle' => 'Dashboard',
            'totalUsers' => $this->safe(fn () => User::count(), 0),
            'totalDocuments' => $this->safe(fn () => Document::count(), 0),
            'totalRevenue' => $this->safe(fn () => Payment::totalRevenue(), 0),
            'activeSubscriptions' => $this->safe(fn () => Subscription::count(['status' => 'active']), 0),
            'openTickets' => $this->safe(fn () => SupportTicket::count(['status' => 'open']), 0),
            'newMessages' => $this->safe(fn () => ContactMessage::count(['status' => 'new']), 0),
            'recentUsers' => $this->safe(fn () => User::paginateAll(1, 5), []),
            'recentPayments' => $this->safe(fn () => Payment::recent(8), []),
            'recentActivity' => $this->safe(fn () => ActivityLog::recent(10), []),
        ], 'layouts/admin');
    }

    private function safe(callable $fn, mixed $fallback): mixed
    {
        try {
            return $fn();
        } catch (Throwable $e) {
            Logger::error('Admin dashboard metric failed: ' . $e->getMessage());

            return $fallback;
        }
    }
}
