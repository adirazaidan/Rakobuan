<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'table_number',
        'notes',
        'status',
        'dining_table_id',
        'session_id',
    ];

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }

        public function getIsOverdueAttribute(): bool
    {
        // Kondisi 1: Item belum selesai diantar
        $isPending = $this->quantity > $this->quantity_delivered;

        // Kondisi 2: Sudah lebih dari 15 menit sejak item ini dibuat
        $isLate = Carbon::now()->diffInMinutes($this->created_at) > 1;

        // Kembalikan true HANYA jika kedua kondisi terpenuhi
        return $isPending && $isLate;
    }
}