<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;

class AnalyticsController extends Controller
{
    public function index(): void
    {
        $usersByPlan = Database::connection()
            ->query('SELECT plan, COUNT(*) AS c FROM users GROUP BY plan')
            ->fetchAll();

        $documentsByMonth = Database::connection()
            ->query(
                "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS c
                 FROM documents
                 GROUP BY month ORDER BY month DESC LIMIT 12"
            )
            ->fetchAll();

        $topTemplates = Database::connection()
            ->query(
                'SELECT t.name, COUNT(d.id) AS uses
                 FROM document_templates t
                 LEFT JOIN documents d ON d.template_id = t.id
                 GROUP BY t.id ORDER BY uses DESC LIMIT 10'
            )
            ->fetchAll();

        $this->view('admin/analytics/index', [
            'pageTitle' => 'Analytics',
            'totalUsers' => User::count(),
            'totalDocuments' => Document::count(),
            'totalTemplates' => DocumentTemplate::count(),
            'usersByPlan' => $usersByPlan,
            'documentsByMonth' => $documentsByMonth,
            'topTemplates' => $topTemplates,
        ], 'layouts/admin');
    }
}
