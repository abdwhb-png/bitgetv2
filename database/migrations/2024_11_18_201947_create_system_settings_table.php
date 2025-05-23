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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('handling_fee')->default(0.01);
            $table->string('announcement', 500)->nullable();
            $table->text('tcs')->nullable();
            $table->text('about_us')->nullable();
            $table->text('reg_agree')->nullable();
            $table->text('faq')->nullable();
            $table->json('deposit_wallets')->nullable();
            $table->boolean('admin_broadcasting')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};