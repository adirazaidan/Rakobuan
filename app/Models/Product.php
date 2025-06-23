<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'stock',
        'description',
        'image',
        'is_bestseller',
        'is_available',
    ];

    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

        public function activeDiscount()
    {
        return $this->hasOne(Discount::class)->where('is_active', true);
    }
}