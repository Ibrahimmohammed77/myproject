<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'stock',
        'price',
        'is_active',
        'attributes'
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'price'      => 'decimal:2',
        'attributes' => 'array',
    ];




    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function orderProducts()
    {
        return $this->belongsToMany(
            Order::class,
            'order_items',
            "product_id",
            "order_id",
            'id',
            'id'
        )->withPivot([]);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'options'])
            ->withTimestamps();
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeInStock($q)
    {
        return $q->where('stock', '>', 0);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: Str::slug($this->attributes['name'] ?? '');
    }
}
