<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Category;
use Carbon\CarbonImmutable;

class SummaryService
{
    public function __construct(private Transaction $transaction, private Category $category) {}

    public function getMonthlySummary(string $month)
    {
        $startOfMonth = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = $startOfMonth->endOfMonth();
        $monthTransactions = $this->transaction->whereBetween('occurred_on', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString(),
        ])->get();

        $income_total = 0;
        $expense_total = 0;
        $categories = [];

        foreach ($monthTransactions as $monthTransaction) {
            if ($monthTransaction->type === 'income') $income_total += $monthTransaction->amount;
            if ($monthTransaction->type === 'expense') $expense_total += $monthTransaction->amount;
            $category = Category::find($monthTransaction->category_id);

            if (!isset($categories[$category->id])) {
                $categories[$category->id] = [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'type' => $category->type,
                    'total' => 0,
                    'color' => $category->color,
                ];
            }

            $categories[$category->id]['total'] += $monthTransaction->amount;
        }

        return response()->json([
            'data' => [
                'month' => $month,
                'income_total' => $income_total,
                'expense_total' => $expense_total,
                'balance' => $income_total - $expense_total,
                'by_category' => array_values($categories),
            ]
        ]);
    }
}
