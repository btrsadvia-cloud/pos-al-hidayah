<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qris_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 64)->unique();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('invoice_number', 50)->nullable()->index();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('total_amount');
            $table->integer('amount_uniq')->default(0);
            $table->unsignedBigInteger('admin_fee')->default(0);
            $table->unsignedBigInteger('credit_amount')->default(0);
            $table->string('fee_by', 20)->default('user');
            $table->string('qris_method', 50)->nullable();
            $table->text('qr_url')->nullable();
            $table->longText('qris_image')->nullable();
            $table->text('payment_url')->nullable();
            $table->enum('status', ['pending', 'success', 'expired', 'failed'])->default('pending')->index();
            $table->boolean('is_test')->default(false);
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qris_transactions');
    }
};
