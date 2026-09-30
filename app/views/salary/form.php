<?php
$usingOld = !empty($_SESSION['_old']);
$formYear = (int) old('year', $salary['year']);
$formMonth = (int) old('month', $salary['month']);
$formAmount = old('amount', $salary['amount'] !== '' ? format_amount_input((int) $salary['amount']) : '');
$formNotes = (string) old('notes', $salary['notes'] ?? '');
clear_old();
?>
<section class="page-stack narrow">
    <div class="form-intro">
        <a class="back-link" href="index.php?year=<?= e($formYear) ?>">← Kembali ke dashboard</a>
        <span class="hero-kicker">SALARY RECORD</span>
        <h2><?= e($mode === 'create' ? 'Tambah gaji baru' : 'Edit data gaji') ?></h2>
        <p>Isi satu bulan sekali. Record ini akan ikut dihitung otomatis ke total tahunan.</p>
    </div>

    <form class="panel form-panel" method="post" action="index.php?page=salary&action=<?= e($mode === 'create' ? 'store' : 'update') ?>" data-salary-form>
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <?php if ($mode === 'edit'): ?>
            <input type="hidden" name="id" value="<?= e($salary['id']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field-group">
                <label for="year">Tahun</label>
                <input id="year" name="year" type="number" min="2000" max="2100" value="<?= e($formYear) ?>" required>
            </div>
            <div class="field-group">
                <label for="month">Bulan</label>
                <select id="month" name="month" required>
                    <?php for ($month = 1; $month <= 12; $month++): ?>
                        <option value="<?= e($month) ?>" <?= $month === $formMonth ? 'selected' : '' ?>><?= e(month_name($month)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="field-group">
            <label for="amount">Nominal gaji</label>
            <div class="money-input">
                <span>Rp</span>
                <input id="amount" name="amount" type="text" inputmode="numeric" placeholder="8.000.000" value="<?= e($formAmount) ?>" required data-money-input>
            </div>
            <small>Masukkan angka saja. Contoh: 8000000</small>
        </div>

        <div class="field-group">
            <label for="notes">Catatan <span>opsional</span></label>
            <textarea id="notes" name="notes" rows="5" maxlength="500" placeholder="Contoh: Full salary, bonus, freelance, THR, dll."><?= e($formNotes) ?></textarea>
        </div>

        <div class="form-actions">
            <a class="button button-secondary" href="index.php?year=<?= e($formYear) ?>">Batal</a>
            <button class="button button-primary" type="submit">
                <?= e($mode === 'create' ? 'Simpan gaji' : 'Simpan perubahan') ?>
                <span>→</span>
            </button>
        </div>
    </form>
</section>
