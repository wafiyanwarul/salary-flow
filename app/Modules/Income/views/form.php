<?php
$usingOld = !empty($_SESSION['_old']);
$formYear = (int) old('year', $income['year']);
$formMonth = (int) old('month', $income['month']);
$formCategory = (string) old('category', $income['category'] ?? 'Gaji');
$formTitle = (string) old('title', $income['title'] ?? '');
$formAmount = old('amount', $income['amount'] !== '' ? format_amount_input((int) $income['amount']) : '');
$formNotes = (string) old('notes', $income['notes'] ?? '');
clear_old();

$categories = [
    'Gaji' => 'Gaji Pokok',
    'Uang Bulanan' => 'Uang Bulanan',
    'Freelance' => 'Freelance / Sampingan',
    'Bonus' => 'Bonus / THR',
    'Investasi' => 'Hasil Investasi',
    'Lainnya' => 'Pemasukan Lainnya',
];
?>
<section class="page-stack narrow">
    <div class="form-intro">
        <a class="back-link" href="index.php?year=<?= e($formYear) ?>">
            <?= icon('arrow-left') ?> <span>Kembali ke dashboard</span>
        </a>
        <span class="hero-kicker">INCOME RECORD</span>
        <h2><?= e($mode === 'create' ? 'Tambah pemasukan baru' : 'Edit data pemasukan') ?></h2>
        <p>Bisa input lebih dari 1 pemasukan per bulan (misal gaji pokok, uang bulanan, freelance, dll). Semua akan otomatis terakumulasi.</p>
    </div>

    <form class="panel form-panel" method="post" action="index.php?page=income&action=<?= e($mode === 'create' ? 'store' : 'update') ?>">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <?php if ($mode === 'edit'): ?>
            <input type="hidden" name="id" value="<?= e($income['id']) ?>">
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
                <label for="category">Kategori pemasukan</label>
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
                <label for="title">Sumber / Keterangan singkat <span>opsional</span></label>
                <input id="title" name="title" type="text" placeholder="Contoh: Uang bulanan, Gaji PT ABC" value="<?= e($formTitle) ?>">
            </div>
        </div>

        <div class="field-group">
            <label for="amount">Nominal pemasukan</label>
            <div class="money-input">
                <span>Rp</span>
                <input id="amount" name="amount" type="text" inputmode="numeric" placeholder="5.000.000" value="<?= e($formAmount) ?>" required data-money-input>
            </div>
            <small>Masukkan angka saja. Format ribuan otomatis disesuaikan.</small>
        </div>

        <div class="field-group">
            <label for="notes">Catatan <span>opsional</span></label>
            <textarea id="notes" name="notes" rows="4" maxlength="500" placeholder="Contoh: Prorate, tunjangan transport, bonus performa, dll."><?= e($formNotes) ?></textarea>
        </div>

        <div class="form-actions">
            <a class="button button-secondary" href="index.php?year=<?= e($formYear) ?>">Batal</a>
            <button class="button button-primary" type="submit">
                <span><?= e($mode === 'create' ? 'Simpan pemasukan' : 'Simpan perubahan') ?></span>
                <?= icon('arrow-right') ?>
            </button>
        </div>
    </form>
</section>
