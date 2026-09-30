<?php

declare(strict_types=1);

class SalaryRepository
{
    public function __construct(private JsonStorage $storage)
    {
    }

    public function allByYear(int $year): array
    {
        $rows = array_filter(
            $this->storage->all(),
            fn (array $row): bool => (int) $row['year'] === $year
        );

        usort($rows, fn (array $a, array $b): int => (int) $a['month'] <=> (int) $b['month']);
        return array_values($rows);
    }

    public function find(int $id): ?array
    {
        foreach ($this->storage->all() as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }
        return null;
    }

    public function findByMonth(int $year, int $month): ?array
    {
        foreach ($this->storage->all() as $row) {
            if ((int) $row['year'] === $year && (int) $row['month'] === $month) {
                return $row;
            }
        }
        return null;
    }

    public function create(int $year, int $month, int $amount, ?string $notes): int
    {
        $rows = $this->storage->all();
        $ids = array_map(fn (array $row): int => (int) ($row['id'] ?? 0), $rows);
        $id = $ids ? max($ids) + 1 : 1;
        $now = date('Y-m-d H:i:s');

        $rows[] = [
            'id' => $id,
            'year' => $year,
            'month' => $month,
            'amount' => $amount,
            'notes' => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->storage->save($rows);
        return $id;
    }

    public function update(int $id, int $year, int $month, int $amount, ?string $notes): void
    {
        $rows = $this->storage->all();
        $found = false;

        foreach ($rows as &$row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $row['year'] = $year;
                $row['month'] = $month;
                $row['amount'] = $amount;
                $row['notes'] = $notes;
                $row['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        unset($row);

        if (!$found) {
            throw new RuntimeException('Data gaji tidak ditemukan.');
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

    public function totals(int $year): array
    {
        $rows = $this->allByYear($year);
        $amounts = array_map(fn (array $row): int => (int) $row['amount'], $rows);
        $count = count($amounts);
        $total = array_sum($amounts);

        return [
            'total' => $total,
            'average' => $count ? $total / $count : 0,
            'highest' => $count ? max($amounts) : 0,
            'months_recorded' => $count,
        ];
    }

    public function availableYears(): array
    {
        $years = array_map(fn (array $row): int => (int) $row['year'], $this->storage->all());
        return array_values(array_unique($years));
    }
}
