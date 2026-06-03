<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()
            ->get()
            ->keyBy(fn (Category $category) => "{$category->type}:{$category->name}");

        $transactions = [
            ['occurred_on' => '2026-04-25', 'type' => 'income', 'category' => '給与', 'amount' => 315000, 'memo' => '4月給与'],
            ['occurred_on' => '2026-04-28', 'type' => 'expense', 'category' => '食費', 'amount' => 4200, 'memo' => '月末買い物'],

            ['occurred_on' => '2026-05-01', 'type' => 'expense', 'category' => '交通費', 'amount' => 8400, 'memo' => '定期券'],
            ['occurred_on' => '2026-05-03', 'type' => 'expense', 'category' => '食費', 'amount' => 3200, 'memo' => 'スーパー'],
            ['occurred_on' => '2026-05-05', 'type' => 'expense', 'category' => '娯楽', 'amount' => 2500, 'memo' => '映画'],
            ['occurred_on' => '2026-05-10', 'type' => 'expense', 'category' => '日用品', 'amount' => 1800, 'memo' => '洗剤'],
            ['occurred_on' => '2026-05-15', 'type' => 'income', 'category' => '副業', 'amount' => 45000, 'memo' => 'Web制作'],
            ['occurred_on' => '2026-05-20', 'type' => 'expense', 'category' => '食費', 'amount' => 3600, 'memo' => '外食'],
            ['occurred_on' => '2026-05-25', 'type' => 'income', 'category' => '給与', 'amount' => 320000, 'memo' => '5月給与'],
            ['occurred_on' => '2026-05-30', 'type' => 'expense', 'category' => '食費', 'amount' => 4800, 'memo' => 'スーパー'],

            ['occurred_on' => '2026-06-01', 'type' => 'expense', 'category' => '交通費', 'amount' => 8400, 'memo' => '定期券'],
            ['occurred_on' => '2026-06-05', 'type' => 'expense', 'category' => '日用品', 'amount' => 2300, 'memo' => '消耗品'],
        ];

        foreach ($transactions as $transaction) {
            $category = $categories->get("{$transaction['type']}:{$transaction['category']}");

            Transaction::query()->create([
                'occurred_on' => $transaction['occurred_on'],
                'type' => $transaction['type'],
                'category_id' => $category->id,
                'amount' => $transaction['amount'],
                'memo' => $transaction['memo'],
            ]);
        }
    }
}
