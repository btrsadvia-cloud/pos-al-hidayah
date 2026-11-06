<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->enum('type', ['in', 'out'])->index();
            $table->integer('quantity');
            $table->unsignedBigInteger('unit_cost')->nullable()->comment('HPP / Unit cost in Rupiah');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('reason', 255)->comment('Detail or reason');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('movement_date')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

