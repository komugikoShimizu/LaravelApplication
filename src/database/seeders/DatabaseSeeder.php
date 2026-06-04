<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Database\Seeders\TransactionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        \App\Models\Transaction::query()->truncate();
        \App\Models\Category::query()->truncate();

        Schema::enableForeignKeyConstraints();

        $this->call([
            CategorySeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
