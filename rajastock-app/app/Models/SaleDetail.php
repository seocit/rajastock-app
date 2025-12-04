<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'item_id',
        'item_name',
        "item_code",
        'quantity',
        'discount',
        'unit_price',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function salesReturnDetails()
    {
        return $this->hasMany(SalesReturnDetail::class);
    }
}
