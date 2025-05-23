<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuid('ref_id');
            $table->foreignId('from')->nullable()->constrained(table: 'balances')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('to')->nullable()->constrained(table: 'balances')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('from_amount', 15, 8);
            $table->decimal('to_amount', 15, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swaps');
    }
};