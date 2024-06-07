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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('size',255);
            $table->boolean('is_default')->default(0);
            $table->foreignId('product_id')->constrained('products', 'id');
            $table->unsignedInteger('stock');
            $table->unsignedDecimal('idr_price', 10, 2);
            $table->unsignedDecimal('usd_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
