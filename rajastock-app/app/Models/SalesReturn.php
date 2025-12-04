<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use Auditable;
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
