<?php
$usingOld = !empty($_SESSION['_old']);
$formYear = (int) old('year', $expense['year']);
$formMonth = (int) old('month', $expense['month']);
$formCategory = (string) old('category', $expense['category'] ?? 'Pengeluaran Final');
$formTitle = (string) old('title', $expense['title'] ?? '');
$formAmount = old('amount', $expense['amount'] !== '' ? format_amount_input((int) $expense['amount']) : '');
$formNotes = (string) old('notes', $expense['notes'] ?? '');
clear_old();

$categories = [
    'Pengeluaran Final' => 'Pengeluaran Final Bulanan',
    'Kebutuhan Pokok' => 'Kebutuhan Pokok & Makan',
    'Tempat Tinggal' => 'Tempat Tinggal / Kos / Cicilan',
    'Tagihan' => 'Tagihan & Utilitas (Listrik, Wifi, Air)',
    'Belanja' => 'Belanja & Keperluan Pribadi',
    'Transportasi' => 'Transportasi & Kendaraan',
    'Hiburan' => 'Hiburan & Gaya Hidup',
    'Lainnya' => 'Pengeluaran Lainnya',
];
?>
<section class="page-stack narrow">
    <div class="form-intro">
        <a class="back-link" href="index.php?year=<?= e($formYear) ?>&tab=expenses">
            <?= icon('arrow-left') ?> <span>Kembali ke dashboard</span>
        </a>
        <span class="hero-kicker" style="color: var(--danger);">EXPENSE RECORD</span>
        <h2><?= e($mode === 'create' ? 'Tambah pengeluaran bulanan' : 'Edit data pengeluaran') ?></h2>
        <p>Catat pengeluaran final bulanan atau pos pengeluaran tertentu. Nilai ini akan dihitung otomatis terhadap pemasukan untuk mengetahui sisa harta bersih dan savings rate kamu.</p>
    </div>

    <form class="panel form-panel form-panel-expense" method="post" action="index.php?page=expense&action=<?= e($mode === 'create' ? 'store' : 'update') ?>">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <?php if ($mode === 'edit'): ?>
            <input type="hidden" name="id" value="<?= e($expense['id']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field-group">
                <label for="year">Tahun</label>
                <input id="year" name="year" type="number" min="2000" max="2100" value="<?= e($formYear) ?>" required>
            </div>
            <div class="field-group">
                <label for="month">Bulan</label>
                <div class="custom-select" data-custom-select>
                    <input type="hidden" name="month" id="month" value="<?= e($formMonth) ?>">
                    <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="custom-select-value"><?= e(month_name($formMonth)) ?></span>
                        <span class="custom-select-icon"><?= icon('chevron-down') ?></span>
                    </button>
                    <div class="custom-select-popover" role="listbox">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <div class="custom-select-option <?= $m === $formMonth ? 'selected' : '' ?>" data-value="<?= e($m) ?>" role="option">
                                <span><?= e(month_name($m)) ?></span>
                                <span class="check-icon"><?= icon('check') ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-grid">
            <div class="field-group">
                <label for="category">Kategori pengeluaran</label>
                <div class="custom-select" data-custom-select>
                    <input type="hidden" name="category" id="category" value="<?= e($formCategory) ?>">
                    <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="custom-select-value"><?= e($categories[$formCategory] ?? $formCategory) ?></span>
                        <span class="custom-select-icon"><?= icon('chevron-down') ?></span>
                    </button>
                    <div class="custom-select-popover" role="listbox">
                        <?php foreach ($categories as $catKey => $catLabel): ?>
                            <div class="custom-select-option <?= $catKey === $formCategory ? 'selected' : '' ?>" data-value="<?= e($catKey) ?>" role="option">
                                <span><?= e($catLabel) ?></span>
                                <span class="check-icon"><?= icon('check') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="field-group">
                <label for="title">Keterangan singkat <span>opsional</span></label>
                <input id="title" name="title" type="text" placeholder="Contoh: Pengeluaran Final Bulan Ini, Sewa Kos, dll" value="<?= e($formTitle) ?>">
            </div>
        </div>

        <div class="field-group">
            <label for="amount">Nominal pengeluaran</label>
            <div class="money-input money-input-expense">
                <span>Rp</span>
                <input id="amount" name="amount" type="text" inputmode="numeric" placeholder="3.000.000" value="<?= e($formAmount) ?>" required data-money-input>
            </div>
            <small>Masukkan angka saja. Format ribuan otomatis disesuaikan.</small>
        </div>

        <div class="field-group">
            <label for="notes">Catatan <span>opsional</span></label>
            <textarea id="notes" name="notes" rows="4" maxlength="500" placeholder="Contoh: Total pengeluaran hidup, makan harian, belanja bulanan, dll."><?= e($formNotes) ?></textarea>
        </div>

        <div class="form-actions">
            <a class="button button-secondary" href="index.php?year=<?= e($formYear) ?>&tab=expenses">Batal</a>
            <button class="button button-expense" type="submit">
                <span><?= e($mode === 'create' ? 'Simpan pengeluaran' : 'Simpan perubahan') ?></span>
                <?= icon('arrow-right') ?>
            </button>
        </div>
    </form>
</section>
