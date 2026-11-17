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
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('unit')->default('pcs');
            $table->string('purchase_unit')->nullable();
            $table->unsignedInteger('units_per_purchase')->default(1);
            $table->unsignedInteger('buy_price')->default(0);
            $table->string('buy_price_unit')->default('base');
            $table->unsignedInteger('sell_price')->default(0);
            $table->unsignedInteger('bulk_price')->nullable();
            $table->integer('stock')->default(0);
            $table->string('supplier')->nullable();
            $table->timestamps();
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
