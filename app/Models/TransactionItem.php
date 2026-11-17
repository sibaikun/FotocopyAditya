<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'item_type', 'product_id', 'service_id',
        'item_name', 'qty', 'unit', 'base_qty', 'price', 'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}