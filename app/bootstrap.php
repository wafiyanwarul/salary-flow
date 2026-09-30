<?php

declare(strict_types=1);

use App\Core\Autoloader;
use App\Core\JsonStorage;
use App\Modules\Dashboard\DashboardController;
use App\Modules\Dashboard\DashboardService;
use App\Modules\Expense\ExpenseController;
use App\Modules\Expense\ExpenseRepository;
use App\Modules\Income\IncomeController;
use App\Modules\Income\IncomeRepository;

$config = require dirname(__DIR__) . '/config/config.php';
date_default_timezone_set($config['timezone'] ?? 'Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/Core/Autoloader.php';
Autoloader::register();

require_once __DIR__ . '/helpers.php';

// Storage Engines
$incomeStorage = new JsonStorage($config['incomes_path'] ?? dirname(__DIR__) . '/storage/salaries.json');
$expenseStorage = new JsonStorage($config['expenses_path'] ?? dirname(__DIR__) . '/storage/expenses.json');

// Repositories
$incomeRepository = new IncomeRepository($incomeStorage);
$expenseRepository = new ExpenseRepository($expenseStorage);

// Services
$dashboardService = new DashboardService($incomeRepository, $expenseRepository);

// Controllers
$dashboardController = new DashboardController($dashboardService, $config);
$incomeController = new IncomeController($incomeRepository, $config);
$expenseController = new ExpenseController($expenseRepository, $config);

// Backward compatibility alias
$salaryController = $incomeController;
