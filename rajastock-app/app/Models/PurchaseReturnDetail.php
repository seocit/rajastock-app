<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'purchase_detail_id',
        'quantity_returned',
        'sub_total',
        'condition',
        'reason',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class);
    }

    public function item()
    {
        return $this->purchaseDetail->item();
    }
}
