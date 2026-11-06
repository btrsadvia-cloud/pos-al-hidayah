<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('product_name', 150)->comment('Snapshot of product name');
            $table->string('unit_name', 50)->comment('Snapshot of unit name');
            $table->unsignedBigInteger('unit_price')->comment('Snapshot of applied unit price');
            $table->integer('quantity');
            $table->unsignedBigInteger('item_discount')->default(0)->comment('Discount per item');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('unit_cost')->nullable()->comment('Snapshot of HPP at sale time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

