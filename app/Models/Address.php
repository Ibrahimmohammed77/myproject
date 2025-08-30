<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name','phone','email','line1','line2','city','state','postal_code','country'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }
}
