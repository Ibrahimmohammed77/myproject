<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status', 40)->default('PENDING');
            $table->string('payment_status', 30)->default('UNPAID');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 10)->default('YER');
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index(['status', 'payment_status'], 'idx_orders_statuses');
            $table->index('created_at', 'idx_orders_created_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
