<?php
$total = (int) $totals['total'];
$average = (int) round((float) $totals['average']);
$highest = (int) $totals['highest'];
$recorded = (int) $totals['months_recorded'];
$progress = min(100, round(($recorded / 12) * 100));
$latest = $records ? $records[count($records) - 1] : null;
?>
<section class="page-stack">
    <div class="hero-card">
        <div class="hero-copy">
            <span class="hero-kicker">YEARLY OVERVIEW</span>
            <h2><?= e(format_idr($total)) ?></h2>
            <p>Total salary yang sudah tercatat untuk <?= e((string) $year) ?>.</p>
        </div>
        <form class="year-picker" method="get">
            <input type="hidden" name="page" value="dashboard">
            <label for="year">Tahun</label>
            <div class="select-wrap">
                <select id="year" name="year" onchange="this.form.submit()">
                    <?php foreach ($availableYears as $availableYear): ?>
                        <option value="<?= e($availableYear) ?>" <?= $availableYear === $year ? 'selected' : '' ?>><?= e($availableYear) ?></option>
                    <?php endforeach; ?>
                </select>
                <span>⌄</span>
            </div>
        </form>
    </div>

    <div class="stats-grid">
        <article class="stat-card">
            <div class="stat-label">Rata-rata / bulan</div>
            <div class="stat-value"><?= e(format_idr($average)) ?></div>
            <div class="stat-sub">Dari <?= e($recorded) ?> bulan tercatat</div>
        </article>
        <article class="stat-card">
            <div class="stat-label">Gaji tertinggi</div>
            <div class="stat-value"><?= e(format_idr($highest)) ?></div>
            <div class="stat-sub">Nominal terbesar tahun ini</div>
        </article>
        <article class="stat-card stat-card-accent">
            <div class="stat-label">Progress pencatatan</div>
            <div class="stat-value"><?= e($progress) ?>%</div>
            <div class="progress-track"><span style="width: <?= e($progress) ?>%"></span></div>
            <div class="stat-sub"><?= e($recorded) ?> / 12 bulan</div>
        </article>
    </div>

    <div class="content-grid">
        <section class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Monthly income</div>
                    <div class="panel-subtitle">Visual perjalanan gaji selama <?= e((string) $year) ?></div>
                </div>
                <span class="panel-badge">IDR</span>
            </div>

            <div class="bar-chart">
                <?php foreach ($months as $item): ?>
                    <div class="bar-item" title="<?= e($item['label']) ?>: <?= e($item['record'] ? format_idr((int) $item['record']['amount']) : 'Belum ada data') ?>">
                        <div class="bar-track">
                            <div class="bar-fill <?= $item['record'] ? '' : 'empty' ?>" style="height: <?= e($item['height']) ?>%"></div>
                        </div>
                        <span><?= e($item['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel snapshot-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Snapshot</div>
                    <div class="panel-subtitle">Ringkasan cepat data terbaru</div>
                </div>
            </div>

            <?php if ($latest): ?>
                <div class="snapshot-highlight">
                    <span class="snapshot-month"><?= e(month_name((int) $latest['month'])) ?></span>
                    <strong><?= e(format_idr((int) $latest['amount'])) ?></strong>
                    <span class="snapshot-meta">Update terakhir di <?= e(date('d M Y', strtotime($latest['updated_at']))) ?></span>
                </div>
            <?php else: ?>
                <div class="empty-state compact">
                    <div class="empty-icon">◌</div>
                    <strong>Belum ada data</strong>
                    <span>Mulai dari gaji bulan pertama.</span>
                    <a class="button button-dark" href="index.php?page=salary&action=create">Input gaji</a>
                </div>
            <?php endif; ?>

            <div class="mini-list">
                <div><span>Total bulan</span><strong><?= e($recorded) ?></strong></div>
                <div><span>Rata-rata</span><strong><?= e(format_idr($average)) ?></strong></div>
                <div><span>Tertinggi</span><strong><?= e(format_idr($highest)) ?></strong></div>
            </div>
        </section>
    </div>

    <section class="panel table-panel">
        <div class="panel-header">
            <div>
                <div class="panel-title">Riwayat gaji</div>
                <div class="panel-subtitle">Semua pemasukan yang sudah kamu input</div>
            </div>
            <a class="button button-secondary" href="index.php?page=salary&action=create">＋ Tambah</a>
        </div>

        <?php if (!$records): ?>
            <div class="empty-state">
                <div class="empty-icon large">◎</div>
                <strong>Belum ada salary record</strong>
                <span>Input April sekarang, lanjut Mei, Juni, dan seterusnya. Semuanya akan otomatis terakumulasi.</span>
                <a class="button button-primary" href="index.php?page=salary&action=create">Tambah gaji pertama</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Nominal</th>
                        <th>Catatan</th>
                        <th class="align-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td>
                                <div class="month-cell">
                                    <span class="month-number"><?= e(str_pad((string) $record['month'], 2, '0', STR_PAD_LEFT)) ?></span>
                                    <div>
                                        <strong><?= e(month_name((int) $record['month'])) ?></strong>
                                        <span><?= e($record['year']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="amount-cell"><?= e(format_idr((int) $record['amount'])) ?></td>
                            <td class="notes-cell"><?= e($record['notes'] ?: '—') ?></td>
                            <td class="align-right">
                                <div class="row-actions">
                                    <a class="icon-button" href="index.php?page=salary&action=edit&id=<?= e($record['id']) ?>" aria-label="Edit">✎</a>
                                    <form method="post" action="index.php?page=salary&action=delete" class="inline-form" onsubmit="return confirm('Hapus data gaji ini?')">
                                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= e($record['id']) ?>">
                                        <button class="icon-button danger" type="submit" aria-label="Delete">⌫</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>
