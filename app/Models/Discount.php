<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'product_id',
        'percentage',
        'is_active',
    ];

    /**
     * Mendapatkan produk yang memiliki diskon ini.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}