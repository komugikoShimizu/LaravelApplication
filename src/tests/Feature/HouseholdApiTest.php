<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Category;
use App\Models\Transaction;

class HouseholdApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_categories()
    {
        Category::create([
            'name' => '食費',
            'type' => 'expense',
            'color' => '#dc2626',
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', '食費')
            ->assertJsonPath('data.0.type', 'expense')
            ->assertJsonPath('data.0.color', '#dc2626');
    }

    public function test_post_transactions()
    {
        $category = Category::create([
            'name' => '食費',
            'type' => 'expense',
            'color' => '#dc2626',
        ]);

        $response = $this->postJson('/api/transactions', [
            'occurred_on' => '2026-05-30',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 4800,
            'memo' => 'スーパー',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.occurred_on', '2026-05-30')
            ->assertJsonPath('data.type', 'expense')
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.category_name', '食費')
            ->assertJsonPath('data.amount', 4800)
            ->assertJsonPath('data.memo', 'スーパー');

        $this->assertDatabaseHas('transactions', [
            'category_id' => $category->id,
            'amount' => 4800,
            'memo' => 'スーパー',
        ]);
    }

    public function test_get_transactions()
    {
        $category = Category::create([
            'name' => '食費',
            'type' => 'expense',
            'color' => '#dc2626',
        ]);

        $transaction = Transaction::create([
            'occurred_on' => '2026-05-30',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 4800,
            'memo' => 'スーパー',
        ]);

        $response = $this->getJson('/api/transactions?month=2026-05');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $transaction->id)
            ->assertJsonPath('data.0.occurred_on', '2026-05-30')
            ->assertJsonPath('data.0.type', 'expense')
            ->assertJsonPath('data.0.category_id', $category->id)
            ->assertJsonPath('data.0.amount', 4800)
            ->assertJsonPath('data.0.memo', 'スーパー');
    }

    public function test_get_monthly_summary()
    {
        $category = Category::create([
            'name' => '食費',
            'type' => 'expense',
            'color' => '#dc2626',
        ]);

        $transaction = Transaction::create([
            'occurred_on' => '2026-05-30',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 4800,
            'memo' => 'スーパー',
        ]);

        $response = $this->getJson('/api/summaries/monthly?month=2026-05');

        $response->assertStatus(200)
            ->assertJsonPath('data.expense_total', 4800)
            ->assertJsonPath('data.balance', -4800)
            ->assertJsonPath('data.by_category.0.total', 4800);
    }
}
