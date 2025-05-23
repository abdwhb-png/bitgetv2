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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuid('ref_id');
            $table->string('type');
            $table->decimal('amount')->nullable();
            $table->decimal('converted_amount', 15, 8)->nullable();
            $table->string('method')->nullable();
            $table->decimal('balance_on_create', 15, 8)->nullable();
            $table->decimal('handling_fee')->nullable();
            $table->foreignId('asset_id')->nullable()->constrained(table: 'payment_methods')->cascadeOnUpdate()->nullOnDelete();
            $table->string('binded_address')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('completed')->nullable();
            $table->string('deleted_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
