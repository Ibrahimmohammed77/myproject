<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->integer('stock')->default(0)->check('stock >= 0');
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->json('attributes')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index(['category_id', 'is_active'], 'idx_products_category_active');
            $table->index('price', 'idx_products_price');

            $table->foreignId("category_id")->constrained('categories')->cascadeOnDelete();
        });

    }
    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
