<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('tier', ['retail', 'wholesale', 'super_wholesale']);
            $table->unsignedInteger('min_qty')->default(1);
            $table->unsignedBigInteger('price')->comment('Price in Rupiah integer');
            $table->timestamps();

            $table->unique(['product_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};

