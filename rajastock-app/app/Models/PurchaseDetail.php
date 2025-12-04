<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use Auditable;

    protected $fillable = [
        'purchases_id',
        'item_id',
        'item_name',
        'item_code',
        'quantity',
        'discount',
        'unit_price',
        'subtotal',
    ];

    public function purchases()
    {
        return $this->belongsTo(Purchase::class, 'purchases_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
