<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => '給与', 'type' => 'income', 'color' => '#2563eb'],
            ['name' => '副業', 'type' => 'income', 'color' => '#16a34a'],
            ['name' => '食費', 'type' => 'expense', 'color' => '#dc2626'],
            ['name' => '日用品', 'type' => 'expense', 'color' => '#ea580c'],
            ['name' => '交通費', 'type' => 'expense', 'color' => '#7c3aed'],
            ['name' => '娯楽', 'type' => 'expense', 'color' => '#0891b2'],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
