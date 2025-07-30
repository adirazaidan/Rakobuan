<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $appends = ['translated_status'];
    
    protected $fillable = [
        'customer_name',
        'table_number',
        'status',
        'total_price',
        'dining_table_id',
        'session_id',
        'order_number',
    ];

    /**
     * Mendefinisikan relasi bahwa satu Order memiliki banyak OrderItem.
     * Nama method (orderItems) harus sama persis dengan yang kita panggil di controller.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function getTranslatedStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Belum Diproses',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

protected static function boot()
{
    parent::boot();

    static::creating(function ($order) {
        // Format: YYDDD-NN (Tahun, Hari ke-, Nomor urut)
        // Contoh: 25211-01 (Pesanan pertama di hari ke-211 tahun 2025)
        $datePart = now()->format('yz'); // y = tahun (2 digit), z = hari ke- (0-365)
        $todayOrderCount = Order::whereDate('created_at', today())->count();
        $sequence = str_pad($todayOrderCount + 1, 2, '0', STR_PAD_LEFT);
        
        $order->order_number = 'OD' . $datePart . $sequence;
    });
}
}