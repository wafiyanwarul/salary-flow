<?php

declare(strict_types=1);

namespace App\Modules\Dashboard;

class DashboardController
{
    public function __construct(
        private DashboardService $dashboardService,
        private array $config,
    ) {
    }

    public function index(): void
    {
        $year = max(2000, min(2100, (int) ($_GET['year'] ?? current_year())));
        $activeTab = $_GET['tab'] ?? 'summary';
        $data = $this->dashboardService->getDashboardData($year);

        $pageTitle = 'Dashboard';
        $config = $this->config;
        $view = __DIR__ . '/views/index.php';
        require dirname(__DIR__, 2) . '/views/layouts/app.php';
    }
}
