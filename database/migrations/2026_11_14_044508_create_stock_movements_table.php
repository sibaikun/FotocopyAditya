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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->integer('qty');
            $table->unsignedInteger('input_qty');
            $table->string('input_unit');
            $table->unsignedInteger('total_cost')->default(0);
            $table->string('reason')->nullable()->index();                      // barang keluar: sale, damaged, lost, own_use, other
            $table->unsignedBigInteger('transaction_id')->nullable()->index();  // nota penjualan (khusus reason = sale)
            $table->string('note')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
