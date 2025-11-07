<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_number',
        'purchase_id',
        'return_date',
        'description',
        'total_returned_amount',
        'status',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function details()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }
}
