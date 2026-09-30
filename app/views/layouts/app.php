<?php
/** @var string $view */
/** @var array $config */

$success = flash('success');
$error = flash('error');
$title = $pageTitle ?? $config['app_name'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title) ?> · <?= e($config['app_name']) ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="alternate icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="app-shell">
    <!-- Desktop & Tablet Sidebar -->
    <aside class="sidebar">
        <a href="index.php" class="brand">
            <div class="brand-mark">S</div>
            <div>
                <strong>SalaryFlow</strong>
                <span>Personal Cashflow</span>
            </div>
        </a>

        <nav class="nav">
            <a class="nav-link <?= is_route('dashboard') ? 'active' : '' ?>" href="index.php">
                <span class="nav-icon"><?= icon('home') ?></span>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?= is_route('income') ? 'active' : '' ?>" href="index.php?page=income&action=create">
                <span class="nav-icon text-accent"><?= icon('plus') ?></span>
                <span>Tambah Pemasukan</span>
            </a>
            <a class="nav-link <?= is_route('expense') ? 'active' : '' ?>" href="index.php?page=expense&action=create">
                <span class="nav-icon text-danger"><?= icon('minus') ?></span>
                <span>Tambah Pengeluaran</span>
            </a>
        </nav>

        <div class="sidebar-tip">
            <span class="tip-dot"></span>
            <div>
                <strong>Cashflow & Savings</strong>
                <p>Catat pemasukan & pengeluaran untuk memantau sisa harta bersih dan target tabunganmu.</p>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Mobile Top App Bar (visible on < 900px) -->
        <div class="mobile-header">
            <a href="index.php" class="brand mobile-brand">
                <div class="brand-mark sm">S</div>
                <div>
                    <strong>SalaryFlow</strong>
                    <span>Cashflow Tracker</span>
                </div>
            </a>
            <div class="mobile-actions">
                <a href="index.php?page=income&action=create" class="button button-sm button-primary" title="Tambah Pemasukan">
                    <?= icon('plus') ?> <span>Masuk</span>
                </a>
                <a href="index.php?page=expense&action=create" class="button button-sm button-expense" title="Tambah Pengeluaran">
                    <?= icon('minus') ?> <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Page Topbar -->
        <header class="topbar">
            <div>
                <p class="eyebrow">PERSONAL FINANCE TRACKER</p>
                <h1><?= e($title) ?></h1>
            </div>
            <div class="topbar-actions">
                <a href="index.php?page=income&action=create" class="button button-primary">
                    <?= icon('plus') ?> <span>Tambah Pemasukan</span>
                </a>
                <a href="index.php?page=expense&action=create" class="button button-expense">
                    <?= icon('minus') ?> <span>Tambah Pengeluaran</span>
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        <?php if ($success): ?>
            <div class="alert alert-success" data-auto-dismiss>
                <span class="alert-icon"><?= icon('check') ?></span>
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error" data-auto-dismiss>
                <span class="alert-icon"><?= icon('info') ?></span>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Active View -->
        <?php require $view; ?>
    </main>

    <!-- Mobile Bottom Navigation for Thumb Comfort on Phones -->
    <nav class="mobile-bottom-nav">
        <a href="index.php" class="bottom-nav-item <?= is_route('dashboard') ? 'active' : '' ?>">
            <span class="bottom-nav-icon"><?= icon('home') ?></span>
            <span>Dashboard</span>
        </a>
        <a href="index.php?page=income&action=create" class="bottom-nav-item <?= is_route('income') ? 'active' : '' ?>">
            <span class="bottom-nav-icon text-accent"><?= icon('plus') ?></span>
            <span>Pemasukan</span>
        </a>
        <a href="index.php?page=expense&action=create" class="bottom-nav-item <?= is_route('expense') ? 'active' : '' ?>">
            <span class="bottom-nav-icon text-danger"><?= icon('minus') ?></span>
            <span>Pengeluaran</span>
        </a>
    </nav>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
