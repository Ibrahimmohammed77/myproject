<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('path', 255);
            $table->boolean('is_main')->default(false);
            $table->integer('sort_order')->default(0)->check('sort_order >= 0');
            $table->timestampsTz();
            $table->index(['product_id', 'is_main'], 'idx_product_images_main');
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_images');
    }
};
