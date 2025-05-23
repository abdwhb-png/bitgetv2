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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('order_type_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuid('ref_id');
            $table->string('symbol');
            $table->string('quote');
            $table->string('base');
            $table->string('asset');
            $table->decimal('quantity', 15, 8);
            $table->decimal('open_price', 15, 8);
            $table->bigInteger('open_time');
            $table->bigInteger('expiration');
            $table->decimal('profit', 15, 8)->default(0);
            $table->string('profit_type')->nullable();
            $table->decimal('percentage')->nullable();
            $table->decimal('close_price', 15, 8)->nullable();
            $table->bigInteger('close_time')->nullable();
            $table->bigInteger('opened_at')->nullable();
            $table->bigInteger('closed_at')->nullable();
            $table->decimal('balance_on_create', 15, 8)->decimal();
            $table->decimal('sl', 15, 8)->nullable();
            $table->decimal('tp', 15, 8)->nullable();
            $table->string('market')->default('spot')->nullable();
            $table->boolean('by_admin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
