<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'purchase_code',
        'supplier_id',
        'purchase_date',
        'total_amount',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchases_id');
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($purchase) {

            // Kembalikan stok untuk setiap detail yang pernah masuk
            foreach ($purchase->details as $detail) {
                $detail->item->stock -= $detail->quantity;
                $detail->item->save();
            }

            // Hapus detail-nya
            $purchase->details()->delete();
        });
    }

}
