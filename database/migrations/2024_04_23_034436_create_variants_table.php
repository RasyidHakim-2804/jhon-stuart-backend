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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('product_id')->constrained('products', 'id');
            $table->unsignedSmallInteger('stock');
            $table->unsignedSmallInteger('max_order')->nullable();
            $table->unsignedSmallInteger('min_order')->default('1');
            $table->decimal('idr_price', 10, 2);
            $table->decimal('usd_price', 10, 2);
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
