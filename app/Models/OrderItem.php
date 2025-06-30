<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'quantity_delivered',
        'price',
        'notes',
    ];

    /**
     * Mendefinisikan relasi bahwa satu OrderItem dimiliki oleh satu Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu OrderItem merujuk ke satu Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        // Kondisi 1: Item belum selesai diantar
        $isPending = $this->quantity > $this->quantity_delivered;

        // Kondisi 2: Sudah lebih dari 15 menit sejak item ini dibuat
        $isLate = Carbon::now()->diffInMinutes($this->created_at) > 15;

        // Kembalikan true HANYA jika kedua kondisi terpenuhi
        return $isPending && $isLate;
    }
}