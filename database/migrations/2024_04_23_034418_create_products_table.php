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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('category_id')->constrained('product_categories', 'id');
            $table->string('type');
            $table->string('name', 255);
            $table->string('slug')->unique();
            $table->boolean('is_active');
            $table->boolean('is_varied');
            $table->text('description');
            $table->text('short_description');
            $table->unsignedInteger('popular')->default(0);
            $table->unsignedDecimal('rating',3, 1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
