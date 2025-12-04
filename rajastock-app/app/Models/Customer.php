<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'address',
        'no_contact',
        'email',       
    ];
}
