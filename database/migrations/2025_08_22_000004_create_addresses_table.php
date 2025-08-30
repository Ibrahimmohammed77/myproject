<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('phone', 40);
            $table->string('email', 150)->nullable();
            $table->string('line1', 200);
            $table->string('line2', 200)->nullable();
            $table->string('city', 120);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on("users")->cascadeOnDelete();
            $table->string('state', 120)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('country', 120)->default('Yemen');
            $table->timestampsTz();
            $table->index(['city', 'country'], 'idx_addresses_city_country');
        });
    }
    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
