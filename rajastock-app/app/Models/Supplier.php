<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use Auditable;
    use HasFactory;
    protected $fillable = [
          'supplier_code',
          'supplier_name',
          'address',
          'no_contact',
          'email'
        ];

    public function purchases(){
      return $this->hasMany(Purchase::class);
    }
}
