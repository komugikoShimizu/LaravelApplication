<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('occurred_on');
            $table->string('type', 20);
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('amount');
            $table->string('memo')->nullable();
            $table->timestamps();

            $table->index(['occurred_on', 'type']);
            $table->index(['category_id', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
