<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 40);
            $table->string('status', 30)->default('PENDING');
            $table->string('transaction_ref', 191)->nullable();
            $table->json('meta')->nullable();
            $table->timestampsTz();
            $table->index(['order_id', 'status', 'method'], 'idx_payments_order_status_method');
            $table->index('created_at', 'idx_payments_created_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
