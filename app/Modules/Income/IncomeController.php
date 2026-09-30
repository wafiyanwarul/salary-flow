<?php

declare(strict_types=1);

namespace App\Modules\Income;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class IncomeController
{
    public function __construct(
        private IncomeRepository $incomeRepository,
        private array $config,
    ) {
    }

    public function createForm(): void
    {
        $selectedYear = (int) ($_GET['year'] ?? current_year());
        $selectedMonth = (int) ($_GET['month'] ?? current_month());
        $income = [
            'year' => $selectedYear,
            'month' => $selectedMonth,
            'category' => $_GET['category'] ?? 'Gaji',
            'title' => '',
            'amount' => '',
            'notes' => '',
        ];
        $mode = 'create';
        $pageTitle = 'Tambah pemasukan';
        $config = $this->config;
        $view = __DIR__ . '/views/form.php';
        require dirname(__DIR__, 2) . '/views/layouts/app.php';
    }

    public function editForm(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $income = $this->incomeRepository->find($id);

        if (!$income) {
            flash('error', 'Data pemasukan tidak ditemukan.');
            redirect('index.php');
        }

        $mode = 'edit';
        $pageTitle = 'Edit pemasukan';
        $config = $this->config;
        $view = __DIR__ . '/views/form.php';
        require dirname(__DIR__, 2) . '/views/layouts/app.php';
    }

    public function store(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $data = $this->validatePayload($_POST);

        try {
            $this->incomeRepository->create(
                $data['year'],
                $data['month'],
                $data['category'],
                $data['amount'],
                $data['notes'],
                $data['title']
            );
            clear_old();
            flash('success', 'Data pemasukan berhasil ditambahkan.');
            redirect('index.php?year=' . $data['year']);
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=income&action=create');
        }
    }

    public function update(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatePayload($_POST);

        try {
            $existing = $this->incomeRepository->find($id);
            if (!$existing) {
                throw new RuntimeException('Data pemasukan tidak ditemukan.');
            }

            $this->incomeRepository->update(
                $id,
                $data['year'],
                $data['month'],
                $data['category'],
                $data['amount'],
                $data['notes'],
                $data['title']
            );
            clear_old();
            flash('success', 'Data pemasukan berhasil diperbarui.');
            redirect('index.php?year=' . $data['year']);
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=income&action=edit&id=' . $id);
        }
    }

    public function destroy(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $record = $this->incomeRepository->find($id);

        if ($record) {
            $this->incomeRepository->delete($id);
            flash('success', 'Data pemasukan berhasil dihapus.');
            redirect('index.php?year=' . (int) $record['year']);
        }

        flash('error', 'Data pemasukan tidak ditemukan.');
        redirect('index.php');
    }

    private function validatePayload(array $payload): array
    {
        $year = filter_var($payload['year'] ?? null, FILTER_VALIDATE_INT);
        $month = filter_var($payload['month'] ?? null, FILTER_VALIDATE_INT);
        $amountRaw = str_replace(['.', ',', 'Rp', ' '], '', (string) ($payload['amount'] ?? ''));
        $amount = filter_var($amountRaw, FILTER_VALIDATE_INT);
        $category = trim((string) ($payload['category'] ?? 'Gaji'));
        $title = trim((string) ($payload['title'] ?? ''));
        $notes = trim((string) ($payload['notes'] ?? ''));

        if ($year === false || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException('Tahun harus berada di antara 2000 dan 2100.');
        }
        if ($month === false || $month < 1 || $month > 12) {
            throw new InvalidArgumentException('Bulan tidak valid.');
        }
        if ($amount === false || $amount < 0) {
            throw new InvalidArgumentException('Nominal pemasukan harus berupa angka 0 atau lebih.');
        }
        if ($category === '') {
            $category = 'Gaji';
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
