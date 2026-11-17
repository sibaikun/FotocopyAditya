<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    // Alasan barang keluar
    public const OUT_REASONS = [
        'sale'    => 'Penjualan',
        'damaged' => 'Rusak',
        'lost'    => 'Hilang',
        'own_use' => 'Dipakai sendiri',
        'other'   => 'Lainnya',
    ];

    protected $fillable = [
        'product_id', 'type', 'qty', 'input_qty', 'input_unit',
        'total_cost', 'reason', 'transaction_id', 'note', 'date',
    ];

    // Alasan yang boleh dipilih di form manual (penjualan hanya dari Kasir)
    public static function manualReasons(): array
    {
        return array_diff_key(self::OUT_REASONS, ['sale' => true]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}