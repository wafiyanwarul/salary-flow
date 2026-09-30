<?php

declare(strict_types=1);

namespace App\Modules\Dashboard;

use App\Modules\Expense\ExpenseRepository;
use App\Modules\Income\IncomeRepository;

class DashboardService
{
    public function __construct(
        private IncomeRepository $incomeRepository,
        private ExpenseRepository $expenseRepository,
    ) {
    }

    public function getDashboardData(int $year): array
    {
        $incomes = $this->incomeRepository->allByYear($year);
        $expenses = $this->expenseRepository->allByYear($year);

        // Group by month
        $monthlyIncomes = array_fill(1, 12, []);
        $monthlyExpenses = array_fill(1, 12, []);
        $monthlyIncomeTotals = array_fill(1, 12, 0);
        $monthlyExpenseTotals = array_fill(1, 12, 0);

        foreach ($incomes as $inc) {
            $m = (int) $inc['month'];
            if ($m >= 1 && $m <= 12) {
                $monthlyIncomes[$m][] = $inc;
                $monthlyIncomeTotals[$m] += (int) $inc['amount'];
            }
        }

        foreach ($expenses as $exp) {
            $m = (int) $exp['month'];
            if ($m >= 1 && $m <= 12) {
                $monthlyExpenses[$m][] = $exp;
                $monthlyExpenseTotals[$m] += (int) $exp['amount'];
            }
        }

        $totalIncome = array_sum($monthlyIncomeTotals);
        $totalExpense = array_sum($monthlyExpenseTotals);
        $netSavings = $totalIncome - $totalExpense;

        $savingsRate = $totalIncome > 0
            ? round(($netSavings / $totalIncome) * 100, 1)
            : 0.0;

        $recordedMonths = 0;
        for ($m = 1; $m <= 12; $m++) {
            if ($monthlyIncomeTotals[$m] > 0 || $monthlyExpenseTotals[$m] > 0) {
                $recordedMonths++;
            }
        }

        $avgIncome = $recordedMonths > 0 ? (int) round($totalIncome / $recordedMonths) : 0;
        $avgExpense = $recordedMonths > 0 ? (int) round($totalExpense / $recordedMonths) : 0;
        $avgNet = $recordedMonths > 0 ? (int) round($netSavings / $recordedMonths) : 0;

        $highestIncome = max($monthlyIncomeTotals);
        $highestExpense = max($monthlyExpenseTotals);

        // Chart scaling: max value between income and expense
        $chartMax = max($highestIncome, $highestExpense);

        $chartMonths = [];
        $latestRecordedMonth = null;

        for ($m = 1; $m <= 12; $m++) {
            $incVal = $monthlyIncomeTotals[$m];
            $expVal = $monthlyExpenseTotals[$m];
            $netVal = $incVal - $expVal;
            $monthSavingsRate = $incVal > 0 ? round(($netVal / $incVal) * 100, 1) : 0.0;

            if ($incVal > 0 || $expVal > 0) {
                $latestRecordedMonth = $m;
            }

            $chartMonths[] = [
                'month' => $m,
                'label' => short_month_name($m),
                'full_name' => month_name($m),
                'income' => $incVal,
                'expense' => $expVal,
                'net' => $netVal,
                'savings_rate' => $monthSavingsRate,
                'has_data' => ($incVal > 0 || $expVal > 0),
                'income_height' => $chartMax > 0 && $incVal > 0 ? max(6, (int) round(($incVal / $chartMax) * 100)) : 0,
                'expense_height' => $chartMax > 0 && $expVal > 0 ? max(6, (int) round(($expVal / $chartMax) * 100)) : 0,
                'incomes' => $monthlyIncomes[$m],
                'expenses' => $monthlyExpenses[$m],
            ];
        }

        // Available years for dropdown
        $incomeYears = $this->incomeRepository->availableYears();
        $expenseYears = $this->expenseRepository->availableYears();
        $availableYears = array_values(array_unique(array_merge($incomeYears, $expenseYears, [current_year()])));
        rsort($availableYears);

        // Latest snapshot data
        $snapshot = null;
        if ($latestRecordedMonth !== null) {
            $snapshot = [
                'month' => $latestRecordedMonth,
                'month_name' => month_name($latestRecordedMonth),
                'income' => $monthlyIncomeTotals[$latestRecordedMonth],
                'expense' => $monthlyExpenseTotals[$latestRecordedMonth],
                'net' => $monthlyIncomeTotals[$latestRecordedMonth] - $monthlyExpenseTotals[$latestRecordedMonth],
                'savings_rate' => $monthlyIncomeTotals[$latestRecordedMonth] > 0
                    ? round((($monthlyIncomeTotals[$latestRecordedMonth] - $monthlyExpenseTotals[$latestRecordedMonth]) / $monthlyIncomeTotals[$latestRecordedMonth]) * 100, 1)
                    : 0.0,
            ];
        }

        return [
            'year' => $year,
            'available_years' => $availableYears,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_savings' => $netSavings,
            'savings_rate' => $savingsRate,
            'recorded_months' => $recordedMonths,
            'avg_income' => $avgIncome,
            'avg_expense' => $avgExpense,
            'avg_net' => $avgNet,
            'highest_income' => $highestIncome,
            'highest_expense' => $highestExpense,
            'chart_max' => $chartMax,
            'chart_months' => $chartMonths,
            'snapshot' => $snapshot,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ];
    }
}
