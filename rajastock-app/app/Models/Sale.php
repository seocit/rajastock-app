<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sale_code',
        'customer_id',
        'sale_date',
        'description',
        'total_amount',
        'is_posted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

      public function user()
    {
        return $this->belongsTo(User::class);
    }
}
