<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Models\Transaction;
use App\Models\Category;

class TransactionService
{
    public function __construct(private Transaction $transaction, private Category $category)
    {
        
    }

    public function createTransaction(array $data)
    {
        $transaction = $this->transaction->create([
            'occurred_on' => $data['occurred_on'],
            'type' => $data['type'],
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'memo' => $data['memo'],
        ]);

        $categoryName = $this->category::find($transaction->category_id)->name;

        return response()->json([
            'data' => [
                'id' => $transaction->id,
                'occurred_on' => Carbon::parse($transaction->occurred_on)->toDateString(),
                'type' => $transaction->type,
                'category_id' => $transaction->category_id,
                'category_name' => $categoryName,
                'amount' => $transaction->amount,
                'memo' => $transaction->memo
            ]
        ]);
    }

    public function getMonthTransactions(string $month)
    {
        $startOfMonth = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = $startOfMonth->endOfMonth();
        $monthTransactions = $this->transaction::with('category')
        ->whereBetween('occurred_on', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString(),
        ])->get();
        
        $response = [];

        foreach ($monthTransactions as $monthTransaction)
        {
            $category = $monthTransaction->category;

            $response[] = [
                'id' => $monthTransaction->id,
                'occurred_on' => Carbon::parse($monthTransaction->occurred_on)->toDateString(),
                'type' => $monthTransaction->type,
                'category_id' => $monthTransaction->category_id,
                'category_name' => $category->name,
                'amount' => $monthTransaction->amount,
                'memo' => $monthTransaction->memo
            ];
        }

        return response()->json([
            'data' => $response
        ]);
    }
}
