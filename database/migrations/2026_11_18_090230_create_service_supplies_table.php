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
        Schema::create('service_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();   // bahan yang terpakai
            $table->unsignedInteger('qty')->default(1);                          // jumlah bahan per 1 jasa
            $table->unique(['service_id', 'product_id']);                        // satu bahan cukup sekali per jasa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_supplies');
    }
};
