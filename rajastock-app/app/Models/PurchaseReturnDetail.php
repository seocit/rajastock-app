<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    use Auditable;
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
