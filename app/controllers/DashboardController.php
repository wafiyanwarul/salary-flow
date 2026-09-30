<?php

declare(strict_types=1);

class DashboardController
{
    public function __construct(
        private SalaryRepository $salaryRepository,
        private array $config,
    ) {
    }

    public function index(): void
    {
        $year = max(2000, min(2100, (int) ($_GET['year'] ?? current_year())));
        $records = $this->salaryRepository->allByYear($year);
        $totals = $this->salaryRepository->totals($year);
        $availableYears = $this->salaryRepository->availableYears();
        if (!in_array(current_year(), $availableYears, true)) {
            $availableYears[] = current_year();
        }
        rsort($availableYears);

        $byMonth = [];
        foreach ($records as $record) {
            $byMonth[(int) $record['month']] = $record;
        }

        $chartMax = 0;
        foreach ($byMonth as $record) {
            $chartMax = max($chartMax, (int) $record['amount']);
        }

        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $record = $byMonth[$month] ?? null;
            $months[] = [
                'month' => $month,
                'label' => short_month_name($month),
                'record' => $record,
                'height' => $chartMax > 0 && $record ? max(8, round(((int) $record['amount'] / $chartMax) * 100)) : 4,
            ];
        }

        $pageTitle = 'Dashboard';
        $config = $this->config;
        $view = dirname(__DIR__) . '/views/dashboard/index.php';
        require dirname(__DIR__) . '/views/layouts/app.php';
    }
}
