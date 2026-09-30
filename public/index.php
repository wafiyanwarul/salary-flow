<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Normalize legacy alias
if ($page === 'salary') {
    $page = 'income';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'income') {
        match ($action) {
            'store' => $incomeController->store(),
            'update' => $incomeController->update(),
            'delete' => $incomeController->destroy(),
            default => (function () {
                http_response_code(405);
                exit('Method Not Allowed');
            })(),
        };
    } elseif ($page === 'expense') {
        match ($action) {
            'store' => $expenseController->store(),
            'update' => $expenseController->update(),
            'delete' => $expenseController->destroy(),
            default => (function () {
                http_response_code(405);
                exit('Method Not Allowed');
            })(),
        };
    } else {
        http_response_code(405);
        exit('Method Not Allowed');
    }
}

switch ($page) {
    case 'income':
        if ($action === 'create') {
            $incomeController->createForm();
            break;
        }
        if ($action === 'edit') {
            $incomeController->editForm();
            break;
        }
        http_response_code(404);
        exit('Not Found');

    case 'expense':
        if ($action === 'create') {
            $expenseController->createForm();
            break;
        }
        if ($action === 'edit') {
            $expenseController->editForm();
            break;
        }
        http_response_code(404);
        exit('Not Found');

    case 'dashboard':
    default:
        $dashboardController->index();
        break;
}
