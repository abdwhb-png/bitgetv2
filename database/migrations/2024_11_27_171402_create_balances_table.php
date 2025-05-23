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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained(table: 'payment_methods')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('wallet')->nullable();
            $table->decimal('amount', 15, 8)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};