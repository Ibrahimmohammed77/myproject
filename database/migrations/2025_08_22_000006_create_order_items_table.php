<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')
            ->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('quantity')->check('quantity > 0');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->json('options')->nullable();
            $table->timestampsTz();
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_items');
    }
};
