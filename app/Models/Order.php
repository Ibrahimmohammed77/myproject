<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const PAYMENT_UNPAID   = 'UNPAID';
    public const PAYMENT_PAID     = 'PAID';
    public const PAYMENT_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'shipping_address_id', 'status', 'payment_status',
        'subtotal', 'discount', 'shipping_fee', 'tax', 'total', 'currency'
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax'          => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function shippingAddress() { return $this->belongsTo(Address::class, 'shipping_address_id'); }
    public function orderItems()      { return $this->hasMany(OrderItem::class); }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['quantity','unit_price','subtotal','options'])
            ->withTimestamps();
    }
    public function payment()         { return $this->hasOne(Payment::class); }

    public function scopeCompleted($q) { return $q->where('status', self::STATUS_COMPLETED); }
    public function scopePaid($q)      { return $q->where('payment_status', self::PAYMENT_PAID); }
}
