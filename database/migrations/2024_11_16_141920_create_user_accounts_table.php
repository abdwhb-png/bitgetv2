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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('account_no');
            $table->string('currency')->default('USDT');
            $table->string('account_type')->default('personnal');
            $table->boolean('can_trade')->default(true);
            $table->string('profit_type')->default('positive');
            $table->integer('profit_min')->default(20);
            $table->integer('profit_max')->default(30);
            $table->string('leverage')->nullable();
            $table->decimal('free_margin')->nullable();
            $table->boolean('mail')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};