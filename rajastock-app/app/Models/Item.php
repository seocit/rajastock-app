<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Item extends Model
{

    use HasFactory;

    protected $fillable = [
        'item_code',
        'merk_id',
        'item_name',
        'price',
        'selling_price',
        'minimum_stock',
        'stock',
        'description',
    ];
    
    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'item_id');
    }
}
