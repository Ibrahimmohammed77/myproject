<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'deleted_at' => 'immutable_datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            // إن لم يصل slug، نولده من name لتفادي الخطأ 1364
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saving(function (Category $category) {
            // لو الاسم تغيّر ولم يرسل slug؛ نحدّث الـ slug تلقائيًا (اختياري)
            if ($category->isDirty('name') && blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
