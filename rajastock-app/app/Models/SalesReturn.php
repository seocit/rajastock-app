<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_code',
        'sale_id',
        'return_date',
        'reason',
        'status',
        'total_amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function details()
    {
        return $this->hasMany(SalesReturnDetail::class);
    }
}
