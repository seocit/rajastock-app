<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchases_id',
        'item_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function purchases()
    {
        return $this->belongsTo(Purchase::class, 'purchases_id');
    }
}
