<?php

declare(strict_types=1);

namespace App\Modules\Expense;

use App\Core\JsonStorage;
use RuntimeException;

class ExpenseRepository
{
    public function __construct(private JsonStorage $storage)
    {
    }

    public function allByYear(int $year): array
    {
        $rows = array_filter(
            $this->allNormalized(),
            fn (array $row): bool => (int) $row['year'] === $year
        );

        usort($rows, function (array $a, array $b): int {
            if ((int) $a['month'] === (int) $b['month']) {
                return (int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0);
            }
            return (int) $a['month'] <=> (int) $b['month'];
        });

        return array_values($rows);
    }

    public function allByMonth(int $year, int $month): array
    {
        $rows = array_filter(
            $this->allNormalized(),
            fn (array $row): bool => (int) $row['year'] === $year && (int) $row['month'] === $month
        );

        return array_values($rows);
    }

    public function find(int $id): ?array
    {
        foreach ($this->allNormalized() as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }
        return null;
    }

    public function create(
        int $year,
        int $month,
        string $category,
        int $amount,
        ?string $notes = null,
        ?string $title = null
    ): int {
        $rows = $this->storage->all();
        $ids = array_map(fn (array $row): int => (int) ($row['id'] ?? 0), $rows);
        $id = $ids ? max($ids) + 1 : 1;
        $now = date('Y-m-d H:i:s');

        $rows[] = [
            'id' => $id,
            'year' => $year,
            'month' => $month,
            'category' => $category,
            'title' => $title ?: $category,
            'amount' => $amount,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->storage->save($rows);
        return $id;
    }

    public function update(
        int $id,
        int $year,
        int $month,
        string $category,
        int $amount,
        ?string $notes = null,
        ?string $title = null
    ): void {
        $rows = $this->storage->all();
        $found = false;

        foreach ($rows as &$row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $row['year'] = $year;
                $row['month'] = $month;
                $row['category'] = $category;
                $row['title'] = $title ?: $category;
                $row['amount'] = $amount;
                $row['notes'] = $notes;
                $row['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        unset($row);

        if (!$found) {
            throw new RuntimeException('Data pengeluaran tidak ditemukan.');
        }

        $this->storage->save($rows);
    }

    public function delete(int $id): void
    {
        $rows = array_values(array_filter(
            $this->storage->all(),
            fn (array $row): bool => (int) ($row['id'] ?? 0) !== $id
        ));
        $this->storage->save($rows);
    }

    public function availableYears(): array
    {
        $years = array_map(fn (array $row): int => (int) $row['year'], $this->storage->all());
        return array_values(array_unique($years));
    }

    private function allNormalized(): array
    {
        $rows = $this->storage->all();
        foreach ($rows as &$row) {
            if (empty($row['category'])) {
                $row['category'] = 'Pengeluaran Final';
            }
            if (empty($row['title'])) {
                $row['title'] = $row['category'];
            }
        }
        unset($row);
        return $rows;
    }
}
