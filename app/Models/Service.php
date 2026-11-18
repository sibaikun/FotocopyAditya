<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'price', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // Bahan yang terpakai setiap jasa ini dijual (qty = jumlah per 1 jasa)
    public function supplies()
    {
        return $this->belongsToMany(Product::class, 'service_supplies')
            ->withPivot('qty')
            ->withTimestamps();
    }
}