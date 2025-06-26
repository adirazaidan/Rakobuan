<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'table_number',
        'notes',
        'status',
        'dining_table_id',
    ];

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }
}