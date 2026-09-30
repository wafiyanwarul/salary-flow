<?php

declare(strict_types=1);

namespace App\Modules\Expense;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ExpenseController
{
    public function __construct(
        private ExpenseRepository $expenseRepository,
        private array $config,
    ) {
    }

    public function createForm(): void
    {
        $selectedYear = (int) ($_GET['year'] ?? current_year());
        $selectedMonth = (int) ($_GET['month'] ?? current_month());
        $expense = [
            'year' => $selectedYear,
            'month' => $selectedMonth,
            'category' => $_GET['category'] ?? 'Pengeluaran Final',
            'title' => '',
            'amount' => '',
            'notes' => '',
        ];
        $mode = 'create';
        $pageTitle = 'Tambah pengeluaran';
        $config = $this->config;
        $view = __DIR__ . '/views/form.php';
        require dirname(__DIR__, 2) . '/views/layouts/app.php';
    }

    public function editForm(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $expense = $this->expenseRepository->find($id);

        if (!$expense) {
            flash('error', 'Data pengeluaran tidak ditemukan.');
            redirect('index.php');
        }

        $mode = 'edit';
        $pageTitle = 'Edit pengeluaran';
        $config = $this->config;
        $view = __DIR__ . '/views/form.php';
        require dirname(__DIR__, 2) . '/views/layouts/app.php';
    }

    public function store(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $data = $this->validatePayload($_POST);

        try {
            $this->expenseRepository->create(
                $data['year'],
                $data['month'],
                $data['category'],
                $data['amount'],
                $data['notes'],
                $data['title']
            );
            clear_old();
            flash('success', 'Data pengeluaran berhasil ditambahkan.');
            redirect('index.php?year=' . $data['year'] . '&tab=expenses');
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=expense&action=create');
        }
    }

    public function update(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatePayload($_POST);

        try {
            $existing = $this->expenseRepository->find($id);
            if (!$existing) {
                throw new RuntimeException('Data pengeluaran tidak ditemukan.');
            }

            $this->expenseRepository->update(
                $id,
                $data['year'],
                $data['month'],
                $data['category'],
                $data['amount'],
                $data['notes'],
                $data['title']
            );
            clear_old();
            flash('success', 'Data pengeluaran berhasil diperbarui.');
            redirect('index.php?year=' . $data['year'] . '&tab=expenses');
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=expense&action=edit&id=' . $id);
        }
    }

    public function destroy(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $record = $this->expenseRepository->find($id);

        if ($record) {
            $this->expenseRepository->delete($id);
            flash('success', 'Data pengeluaran berhasil dihapus.');
            redirect('index.php?year=' . (int) $record['year'] . '&tab=expenses');
        }

        flash('error', 'Data pengeluaran tidak ditemukan.');
        redirect('index.php');
    }

    private function validatePayload(array $payload): array
    {
        $year = filter_var($payload['year'] ?? null, FILTER_VALIDATE_INT);
        $month = filter_var($payload['month'] ?? null, FILTER_VALIDATE_INT);
        $amountRaw = str_replace(['.', ',', 'Rp', ' '], '', (string) ($payload['amount'] ?? ''));
        $amount = filter_var($amountRaw, FILTER_VALIDATE_INT);
        $category = trim((string) ($payload['category'] ?? 'Pengeluaran Final'));
        $title = trim((string) ($payload['title'] ?? ''));
        $notes = trim((string) ($payload['notes'] ?? ''));

        if ($year === false || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException('Tahun harus berada di antara 2000 dan 2100.');
        }
        if ($month === false || $month < 1 || $month > 12) {
            throw new InvalidArgumentException('Bulan tidak valid.');
        }
        if ($amount === false || $amount < 0) {
            throw new InvalidArgumentException('Nominal pengeluaran harus berupa angka 0 atau lebih.');
        }
        if ($category === '') {
            $category = 'Pengeluaran Final';
        }

        return [
            'year' => $year,
            'month' => $month,
            'category' => $category,
            'title' => $title !== '' ? $title : $category,
            'amount' => $amount,
            'notes' => $notes !== '' ? $notes : null,
        ];
    }
}
