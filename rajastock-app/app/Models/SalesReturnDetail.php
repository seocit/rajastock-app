<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnDetail extends Model
{
    protected $fillable = [
        'sales_return_id',
        'sales_detail_id',
        'quantity_returned',
        'sub_total',
        'condition',
        'reason',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function salesDetail()
    {
        return $this->belongsTo(SaleDetail::class);
    }
}
