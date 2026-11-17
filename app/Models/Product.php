<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'image', 'unit', 'purchase_unit', 'units_per_purchase',
        'buy_price', 'buy_price_unit', 'sell_price', 'bulk_price', 'supplier', 'stock',
    ];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Total uang yang dikeluarkan untuk membeli $baseQty item.
    // Dihitung sekali di akhir supaya pembulatan nggak menumpuk.
    public function costFor(int $baseQty): int
    {
        $divisor = $this->buy_price_unit === 'purchase'
            ? max(1, (int) $this->units_per_purchase)
            : 1;

        return (int) round($baseQty * $this->buy_price / $divisor);
    }
}