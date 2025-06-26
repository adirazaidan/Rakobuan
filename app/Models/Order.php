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
    protected $fillable = [
        'customer_name',
        'table_number',
        'status',
        'total_price',
        'dining_table_id',
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
}