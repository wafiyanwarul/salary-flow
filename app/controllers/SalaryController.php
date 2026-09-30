<?php

declare(strict_types=1);

class SalaryController
{
    public function __construct(
        private SalaryRepository $salaryRepository,
        private array $config,
    ) {
    }

    public function createForm(): void
    {
        $selectedYear = (int) ($_GET['year'] ?? current_year());
        $selectedMonth = (int) ($_GET['month'] ?? current_month());
        $salary = [
            'year' => $selectedYear,
            'month' => $selectedMonth,
            'amount' => '',
            'notes' => '',
        ];
        $mode = 'create';
        $pageTitle = 'Tambah gaji';
        $config = $this->config;
        $view = dirname(__DIR__) . '/views/salary/form.php';
        require dirname(__DIR__) . '/views/layouts/app.php';
    }

    public function editForm(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $salary = $this->salaryRepository->find($id);

        if (!$salary) {
            flash('error', 'Data gaji tidak ditemukan.');
            redirect('index.php');
        }

        $mode = 'edit';
        $pageTitle = 'Edit gaji';
        $config = $this->config;
        $view = dirname(__DIR__) . '/views/salary/form.php';
        require dirname(__DIR__) . '/views/layouts/app.php';
    }

    public function store(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $data = $this->validatePayload($_POST);

        try {
            if ($this->salaryRepository->findByMonth($data['year'], $data['month'])) {
                throw new RuntimeException('Gaji untuk bulan tersebut sudah ada. Gunakan Edit untuk memperbaruinya.');
            }

            $this->salaryRepository->create($data['year'], $data['month'], $data['amount'], $data['notes']);
            clear_old();
            flash('success', 'Data gaji berhasil ditambahkan.');
            redirect('index.php?year=' . $data['year']);
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=salary&action=create');
        }
    }

    public function update(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatePayload($_POST);

        try {
            $existing = $this->salaryRepository->find($id);
            if (!$existing) {
                throw new RuntimeException('Data gaji tidak ditemukan.');
            }

            $sameMonth = $this->salaryRepository->findByMonth($data['year'], $data['month']);
            if ($sameMonth && (int) $sameMonth['id'] !== $id) {
                throw new RuntimeException('Sudah ada data gaji untuk bulan tersebut.');
            }

            $this->salaryRepository->update($id, $data['year'], $data['month'], $data['amount'], $data['notes']);
            clear_old();
            flash('success', 'Data gaji berhasil diperbarui.');
            redirect('index.php?year=' . $data['year']);
        } catch (Throwable $e) {
            set_old($_POST);
            flash('error', $e->getMessage());
            redirect('index.php?page=salary&action=edit&id=' . $id);
        }
    }

    public function destroy(): void
    {
        verify_csrf($_POST['_token'] ?? null);
        $id = (int) ($_POST['id'] ?? 0);
        $record = $this->salaryRepository->find($id);

        if ($record) {
            $this->salaryRepository->delete($id);
            flash('success', 'Data gaji berhasil dihapus.');
            redirect('index.php?year=' . (int) $record['year']);
        }

        flash('error', 'Data gaji tidak ditemukan.');
        redirect('index.php');
    }

    private function validatePayload(array $payload): array
    {
        $year = filter_var($payload['year'] ?? null, FILTER_VALIDATE_INT);
        $month = filter_var($payload['month'] ?? null, FILTER_VALIDATE_INT);
        $amountRaw = str_replace(['.', ',', 'Rp', ' '], '', (string) ($payload['amount'] ?? ''));
        $amount = filter_var($amountRaw, FILTER_VALIDATE_INT);
        $notes = trim((string) ($payload['notes'] ?? ''));

        if ($year === false || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException('Tahun harus berada di antara 2000 dan 2100.');
        }
        if ($month === false || $month < 1 || $month > 12) {
            throw new InvalidArgumentException('Bulan tidak valid.');
        }
        if ($amount === false || $amount < 0) {
            throw new InvalidArgumentException('Nominal gaji harus berupa angka 0 atau lebih.');
        }

        return [
            'year' => $year,
            'month' => $month,
            'amount' => $amount,
            'notes' => $notes !== '' ? $notes : null,
        ];
    }
}
