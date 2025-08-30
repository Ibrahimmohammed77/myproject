<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING  = 'PENDING';
    public const STATUS_PAID     = 'PAID';
    public const STATUS_FAILED   = 'FAILED';
    public const STATUS_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'order_id','amount','method','status','transaction_ref','meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta'   => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePaid($q) { return $q->where('status', self::STATUS_PAID); }
}
