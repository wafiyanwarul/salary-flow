<?php
/** @var array $data */
/** @var int $year */
/** @var string $activeTab */

$totalIncome = (int) $data['total_income'];
$totalExpense = (int) $data['total_expense'];
$netSavings = (int) $data['net_savings'];
$savingsRate = (float) $data['savings_rate'];
$recorded = (int) $data['recorded_months'];
$progress = min(100, (int) round(($recorded / 12) * 100));
$snapshot = $data['snapshot'];
$chartMonths = $data['chart_months'];
$incomes = $data['incomes'];
$expenses = $data['expenses'];
$availableYears = $data['available_years'];
?>
<section class="page-stack">
    <!-- Hero Card -->
    <div class="hero-card">
        <div class="hero-copy">
            <span class="hero-kicker">YEARLY NET WORTH & CASHFLOW</span>
            <div class="hero-main-stat">
                <h2 class="<?= $netSavings < 0 ? 'text-danger' : '' ?>"><?= e(format_idr($netSavings)) ?></h2>
                <span class="hero-badge <?= $netSavings >= 0 ? 'badge-success' : 'badge-danger' ?>">
                    <?= $netSavings >= 0 ? 'Surplus Bersih' : 'Defisit' ?>
                </span>
            </div>
            <p>Total harta bersih tersimpan di tahun <?= e((string) $year) ?> (Pemasukan dikurangi Pengeluaran).</p>

            <div class="hero-pills">
                <div class="hero-pill income">
                    <span class="pill-dot"></span>
                    <span class="pill-label">Pemasukan</span>
                    <strong><?= e(format_idr($totalIncome)) ?></strong>
                </div>
                <div class="hero-pill expense">
                    <span class="pill-dot"></span>
                    <span class="pill-label">Pengeluaran</span>
                    <strong><?= e(format_idr($totalExpense)) ?></strong>
                </div>
                <div class="hero-pill rate">
                    <span class="pill-dot"></span>
                    <span class="pill-label">Savings Rate</span>
                    <strong><?= e(number_format($savingsRate, 1, ',', '.')) ?>%</strong>
                </div>
            </div>
        </div>

        <form class="year-picker" method="get">
            <input type="hidden" name="page" value="dashboard">
            <label for="year">Pilih Tahun</label>
            <div class="custom-select" data-custom-select data-auto-submit="true">
                <input type="hidden" name="year" id="year" value="<?= e($year) ?>">
                <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span class="custom-select-value"><?= e($year) ?></span>
                    <span class="custom-select-icon"><?= icon('chevron-down') ?></span>
                </button>
                <div class="custom-select-popover" role="listbox">
                    <?php foreach ($availableYears as $availableYear): ?>
                        <div class="custom-select-option <?= $availableYear === $year ? 'selected' : '' ?>" data-value="<?= e($availableYear) ?>" role="option">
                            <span><?= e($availableYear) ?></span>
                            <span class="check-icon"><?= icon('check') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Stats Grid -->
    <div class="stats-grid">
        <article class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Pemasukan</span>
                <span class="stat-tag tag-income">Income</span>
            </div>
            <div class="stat-value text-accent"><?= e(format_idr($totalIncome)) ?></div>
            <div class="stat-sub">Dari <?= e($recorded) ?> bulan tercatat</div>
        </article>

        <article class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Pengeluaran</span>
                <span class="stat-tag tag-expense">Expense</span>
            </div>
            <div class="stat-value text-danger"><?= e(format_idr($totalExpense)) ?></div>
            <div class="stat-sub">Rata-rata <?= e(format_idr($data['avg_expense'])) ?> / bln</div>
        </article>

        <article class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Sisa Harta Bersih</span>
                <span class="stat-tag <?= $netSavings >= 0 ? 'tag-accent' : 'tag-danger' ?>">Net</span>
            </div>
            <div class="stat-value <?= $netSavings >= 0 ? '' : 'text-danger' ?>"><?= e(format_idr($netSavings)) ?></div>
            <div class="stat-sub">Rata-rata <?= e(format_idr($data['avg_net'])) ?> / bln</div>
        </article>

        <article class="stat-card stat-card-accent">
            <div class="stat-header">
                <span class="stat-label">Savings Rate</span>
                <span class="stat-tag tag-accent"><?= e(number_format($savingsRate, 1, ',', '.')) ?>%</span>
            </div>
            <div class="stat-value"><?= e(number_format($savingsRate, 1, ',', '.')) ?>%</div>
            <div class="progress-track"><span style="width: <?= e(max(0, min(100, (int) $savingsRate))) ?>%"></span></div>
            <div class="stat-sub"><?= $savingsRate >= 20 ? 'Tingkat tabungan sehat' : 'Perlu ditingkatkan' ?> (Target 20%+)</div>
        </article>
    </div>

    <!-- Content Grid (Dual Bar Chart + Snapshot) -->
    <div class="content-grid">
        <section class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Grafik Arus Kas Bulanan</div>
                    <div class="panel-subtitle">Visual perbandingan pemasukan & pengeluaran <?= e((string) $year) ?></div>
                </div>
                <div class="chart-legend">
                    <span class="legend-item"><span class="legend-dot income"></span> Pemasukan</span>
                    <span class="legend-item"><span class="legend-dot expense"></span> Pengeluaran</span>
                </div>
            </div>

            <div class="chart-scroll-wrapper">
                <div class="dual-bar-chart">
                    <?php foreach ($chartMonths as $item): ?>
                        <div class="chart-col" data-tooltip="true">
                            <div class="bars-container">
                                <!-- Income bar track -->
                                <div class="bar-col-track income" title="Pemasukan: <?= e(format_idr($item['income'])) ?>">
                                    <div class="bar-col-fill income <?= $item['income'] > 0 ? '' : 'empty' ?>" style="height: <?= e($item['income_height']) ?>%"></div>
                                </div>
                                <!-- Expense bar track -->
                                <div class="bar-col-track expense" title="Pengeluaran: <?= e(format_idr($item['expense'])) ?>">
                                    <div class="bar-col-fill expense <?= $item['expense'] > 0 ? '' : 'empty' ?>" style="height: <?= e($item['expense_height']) ?>%"></div>
                                </div>
                            </div>
                            <span class="chart-month-label"><?= e($item['label']) ?></span>

                            <!-- Tooltip popup on hover/touch -->
                            <div class="chart-popover">
                                <strong><?= e($item['full_name']) ?> <?= e((string) $year) ?></strong>
                                <div class="popover-row"><span>Pemasukan:</span> <b class="text-accent"><?= e(format_idr($item['income'])) ?></b></div>
                                <div class="popover-row"><span>Pengeluaran:</span> <b class="text-danger"><?= e(format_idr($item['expense'])) ?></b></div>
                                <div class="popover-row border-top"><span>Sisa Bersih:</span> <b><?= e(format_idr($item['net'])) ?></b></div>
                                <div class="popover-row"><span>Savings Rate:</span> <b><?= e(number_format($item['savings_rate'], 1, ',', '.')) ?>%</b></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Snapshot Panel -->
        <section class="panel snapshot-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Snapshot Terbaru</div>
                    <div class="panel-subtitle">Ringkasan kondisi finansial bulan aktif</div>
                </div>
            </div>

            <?php if ($snapshot): ?>
                <div class="snapshot-highlight">
                    <span class="snapshot-month"><?= e($snapshot['month_name']) ?></span>
                    <strong class="<?= $snapshot['net'] < 0 ? 'text-danger' : '' ?>"><?= e(format_idr($snapshot['net'])) ?></strong>
                    <span class="snapshot-meta">Sisa bersih bulan ini</span>
                </div>
                <div class="mini-list">
                    <div><span>Total Pemasukan</span><strong class="text-accent"><?= e(format_idr($snapshot['income'])) ?></strong></div>
                    <div><span>Total Pengeluaran</span><strong class="text-danger"><?= e(format_idr($snapshot['expense'])) ?></strong></div>
                    <div><span>Savings Rate Bulan Ini</span><strong><?= e(number_format($snapshot['savings_rate'], 1, ',', '.')) ?>%</strong></div>
                    <div><span>Bulan Tercatat</span><strong><?= e($recorded) ?> / 12 Bulan</strong></div>
                </div>
            <?php else: ?>
                <div class="empty-state compact">
                    <div class="empty-icon"><?= icon('info') ?></div>
                    <strong>Belum ada transaksi</strong>
                    <span>Mulai catat pemasukan atau pengeluaran pertama kamu.</span>
                    <div class="empty-actions">
                        <a class="button button-primary" href="index.php?page=income&action=create"><?= icon('plus') ?> Pemasukan</a>
                        <a class="button button-expense" href="index.php?page=expense&action=create"><?= icon('minus') ?> Pengeluaran</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Data Tables with Tab Switcher -->
    <section class="panel table-panel">
        <div class="panel-header table-panel-header">
            <div class="tab-buttons" role="tablist">
                <button type="button" class="tab-btn <?= $activeTab === 'summary' ? 'active' : '' ?>" data-tab="tab-summary">
                    <?= icon('chart') ?> <span>Ringkasan Bulanan</span>
                </button>
                <button type="button" class="tab-btn <?= $activeTab === 'incomes' ? 'active' : '' ?>" data-tab="tab-incomes">
                    <?= icon('wallet') ?> <span>Riwayat Pemasukan</span> <span class="tab-count"><?= count($incomes) ?></span>
                </button>
                <button type="button" class="tab-btn <?= $activeTab === 'expenses' ? 'active' : '' ?>" data-tab="tab-expenses">
                    <?= icon('receipt') ?> <span>Riwayat Pengeluaran</span> <span class="tab-count"><?= count($expenses) ?></span>
                </button>
            </div>
            <div class="table-actions">
                <a class="button button-primary" href="index.php?page=income&action=create"><?= icon('plus') ?> <span>Pemasukan</span></a>
                <a class="button button-expense" href="index.php?page=expense&action=create"><?= icon('minus') ?> <span>Pengeluaran</span></a>
            </div>
        </div>

        <!-- Tab 1: Monthly Summary with Item Management -->
        <div id="tab-summary" class="tab-content <?= $activeTab === 'summary' ? 'active' : '' ?>">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Total Pemasukan</th>
                        <th>Total Pengeluaran</th>
                        <th>Sisa Bersih</th>
                        <th>Savings Rate</th>
                        <th>Jumlah Item</th>
                        <th class="align-right">Kelola Item</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($chartMonths as $mRow): 
                        $itemCount = count($mRow['incomes']) + count($mRow['expenses']);
                    ?>
                        <tr class="summary-row <?= $itemCount > 0 ? 'has-items' : '' ?>">
                            <td>
                                <div class="month-cell">
                                    <span class="month-number"><?= e(str_pad((string) $mRow['month'], 2, '0', STR_PAD_LEFT)) ?></span>
                                    <div>
                                        <strong><?= e($mRow['full_name']) ?></strong>
                                        <span><?= e((string) $year) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="amount-cell text-accent"><?= e($mRow['income'] > 0 ? format_idr($mRow['income']) : '—') ?></td>
                            <td class="amount-cell text-danger"><?= e($mRow['expense'] > 0 ? format_idr($mRow['expense']) : '—') ?></td>
                            <td class="amount-cell <?= $mRow['net'] < 0 ? 'text-danger' : '' ?>">
                                <?= e($mRow['has_data'] ? format_idr($mRow['net']) : '—') ?>
                            </td>
                            <td>
                                <?php if ($mRow['income'] > 0): ?>
                                    <span class="savings-badge <?= $mRow['savings_rate'] >= 20 ? 'good' : ($mRow['savings_rate'] > 0 ? 'normal' : 'bad') ?>">
                                        <?= e(number_format($mRow['savings_rate'], 1, ',', '.')) ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-mini"><?= count($mRow['incomes']) ?> masuk · <?= count($mRow['expenses']) ?> keluar</span>
                            </td>
                            <td class="align-right">
                                <?php if ($itemCount > 0): ?>
                                    <button type="button" class="button button-sm button-secondary toggle-month-items" data-target="month-items-<?= $mRow['month'] ?>">
                                        <span>Lihat & Edit Item</span> <?= icon('chevron-down', 'toggle-arrow') ?>
                                    </button>
                                <?php else: ?>
                                    <a class="button button-sm button-secondary" href="index.php?page=income&action=create&year=<?= e($year) ?>&month=<?= e($mRow['month']) ?>">
                                        <?= icon('plus') ?> <span>Tambah</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Expandable Month Item Management Drawer -->
                        <?php if ($itemCount > 0): ?>
                            <tr class="month-items-drawer" id="month-items-<?= $mRow['month'] ?>" style="display: none;">
                                <td colspan="7">
                                    <div class="drawer-content">
                                        <div class="drawer-header">
                                            <strong>Daftar Item Bulan <?= e($mRow['full_name']) ?> <?= e((string) $year) ?></strong>
                                            <div class="drawer-actions">
                                                <a href="index.php?page=income&action=create&year=<?= e($year) ?>&month=<?= e($mRow['month']) ?>" class="button button-sm button-primary">
                                                    <?= icon('plus') ?> Pemasukan
                                                </a>
                                                <a href="index.php?page=expense&action=create&year=<?= e($year) ?>&month=<?= e($mRow['month']) ?>" class="button button-sm button-expense">
                                                    <?= icon('minus') ?> Pengeluaran
                                                </a>
                                            </div>
                                        </div>

                                        <div class="drawer-items-list">
                                            <!-- Incomes of this month -->
                                            <?php foreach ($mRow['incomes'] as $inc): ?>
                                                <div class="drawer-item income">
                                                    <div class="drawer-item-info">
                                                        <span class="category-badge income"><?= e($inc['category'] ?? 'Gaji') ?></span>
                                                        <strong><?= e($inc['title'] ?? $inc['category'] ?? 'Pemasukan') ?></strong>
                                                        <?php if (!empty($inc['notes'])): ?>
                                                            <small class="drawer-notes"><?= e($inc['notes']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="drawer-item-right">
                                                        <span class="amount-cell text-accent">+ <?= e(format_idr((int) $inc['amount'])) ?></span>
                                                        <div class="row-actions">
                                                            <a class="icon-button" href="index.php?page=income&action=edit&id=<?= e($inc['id']) ?>" title="Edit Pemasukan ini">
                                                                <?= icon('pencil') ?>
                                                            </a>
                                                            <form method="post" action="index.php?page=income&action=delete" class="inline-form" onsubmit="return confirm('Hapus pemasukan ini?')">
                                                                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                                                <input type="hidden" name="id" value="<?= e($inc['id']) ?>">
                                                                <button class="icon-button danger" type="submit" title="Hapus">
                                                                    <?= icon('trash') ?>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>

                                            <!-- Expenses of this month -->
                                            <?php foreach ($mRow['expenses'] as $exp): ?>
                                                <div class="drawer-item expense">
                                                    <div class="drawer-item-info">
                                                        <span class="category-badge expense"><?= e($exp['category'] ?? 'Pengeluaran') ?></span>
                                                        <strong><?= e($exp['title'] ?? $exp['category'] ?? 'Pengeluaran') ?></strong>
                                                        <?php if (!empty($exp['notes'])): ?>
                                                            <small class="drawer-notes"><?= e($exp['notes']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="drawer-item-right">
                                                        <span class="amount-cell text-danger">- <?= e(format_idr((int) $exp['amount'])) ?></span>
                                                        <div class="row-actions">
                                                            <a class="icon-button" href="index.php?page=expense&action=edit&id=<?= e($exp['id']) ?>" title="Edit Pengeluaran ini">
                                                                <?= icon('pencil') ?>
                                                            </a>
                                                            <form method="post" action="index.php?page=expense&action=delete" class="inline-form" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                                                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                                                <input type="hidden" name="id" value="<?= e($exp['id']) ?>">
                                                                <button class="icon-button danger" type="submit" title="Hapus">
                                                                    <?= icon('trash') ?>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Incomes List -->
        <div id="tab-incomes" class="tab-content <?= $activeTab === 'incomes' ? 'active' : '' ?>">
            <?php if (!$incomes): ?>
                <div class="empty-state">
                    <div class="empty-icon large"><?= icon('wallet') ?></div>
                    <strong>Belum ada catatan pemasukan untuk <?= e((string) $year) ?></strong>
                    <span>Kamu bisa mencatat gaji bulanan, uang saku/bulanan, bonus, atau freelance.</span>
                    <a class="button button-primary" href="index.php?page=income&action=create"><?= icon('plus') ?> Tambah pemasukan</a>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Kategori & Sumber</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                            <th class="align-right">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($incomes as $inc): ?>
                            <tr>
                                <td>
                                    <div class="month-cell">
                                        <span class="month-number"><?= e(str_pad((string) $inc['month'], 2, '0', STR_PAD_LEFT)) ?></span>
                                        <div>
                                            <strong><?= e(month_name((int) $inc['month'])) ?></strong>
                                            <span><?= e($inc['year']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="source-cell">
                                        <span class="category-badge income"><?= e($inc['category'] ?? 'Gaji') ?></span>
                                        <strong><?= e($inc['title'] ?? $inc['category'] ?? 'Pemasukan') ?></strong>
                                    </div>
                                </td>
                                <td class="amount-cell text-accent"><?= e(format_idr((int) $inc['amount'])) ?></td>
                                <td class="notes-cell"><?= e($inc['notes'] ?: '—') ?></td>
                                <td class="align-right">
                                    <div class="row-actions">
                                        <a class="icon-button" href="index.php?page=income&action=edit&id=<?= e($inc['id']) ?>" aria-label="Edit" title="Edit item ini">
                                            <?= icon('pencil') ?>
                                        </a>
                                        <form method="post" action="index.php?page=income&action=delete" class="inline-form" onsubmit="return confirm('Hapus data pemasukan ini?')">
                                            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= e($inc['id']) ?>">
                                            <button class="icon-button danger" type="submit" aria-label="Delete" title="Hapus item ini">
                                                <?= icon('trash') ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 3: Expenses List -->
        <div id="tab-expenses" class="tab-content <?= $activeTab === 'expenses' ? 'active' : '' ?>">
            <?php if (!$expenses): ?>
                <div class="empty-state">
                    <div class="empty-icon large"><?= icon('receipt') ?></div>
                    <strong>Belum ada catatan pengeluaran untuk <?= e((string) $year) ?></strong>
                    <span>Catat pengeluaran bulanan atau pengeluaran final agar perbandingan grafik dan savings rate kamu muncul!</span>
                    <a class="button button-expense" href="index.php?page=expense&action=create"><?= icon('minus') ?> Tambah pengeluaran</a>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Kategori & Keterangan</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                            <th class="align-right">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($expenses as $exp): ?>
                            <tr>
                                <td>
                                    <div class="month-cell">
                                        <span class="month-number expense"><?= e(str_pad((string) $exp['month'], 2, '0', STR_PAD_LEFT)) ?></span>
                                        <div>
                                            <strong><?= e(month_name((int) $exp['month'])) ?></strong>
                                            <span><?= e($exp['year']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="source-cell">
                                        <span class="category-badge expense"><?= e($exp['category'] ?? 'Pengeluaran') ?></span>
                                        <strong><?= e($exp['title'] ?? $exp['category'] ?? 'Pengeluaran') ?></strong>
                                    </div>
                                </td>
                                <td class="amount-cell text-danger"><?= e(format_idr((int) $exp['amount'])) ?></td>
                                <td class="notes-cell"><?= e($exp['notes'] ?: '—') ?></td>
                                <td class="align-right">
                                    <div class="row-actions">
                                        <a class="icon-button" href="index.php?page=expense&action=edit&id=<?= e($exp['id']) ?>" aria-label="Edit" title="Edit pengeluaran ini">
                                            <?= icon('pencil') ?>
                                        </a>
                                        <form method="post" action="index.php?page=expense&action=delete" class="inline-form" onsubmit="return confirm('Hapus data pengeluaran ini?')">
                                            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= e($exp['id']) ?>">
                                            <button class="icon-button danger" type="submit" aria-label="Delete" title="Hapus pengeluaran ini">
                                                <?= icon('trash') ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</section>
