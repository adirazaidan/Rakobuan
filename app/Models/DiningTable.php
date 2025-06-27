<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'notes', 'is_locked', 'session_id',
    ];

    // Relasi dasar ke SEMUA order milik meja ini
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    // Relasi dasar ke SEMUA panggilan milik meja ini
    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    // Relasi turunan: Mengambil SEMUA pesanan yang sedang aktif
    public function activeOrders()
    {
        return $this->hasMany(Order::class)->whereNotIn('status', ['completed', 'cancelled'])->latest();
    }

    // Relasi turunan: Mengambil SEMUA panggilan yang sedang aktif
    public function activeCalls()
    {
        return $this->hasMany(Call::class)->where('status', '!=', 'completed');
    }

    // public function getSessionHistoryAttribute()
    // {
    //     if (!$this->session_id) {
    //         return collect(); 
    //     }

    //     $orders = $this->orders()
    //                    ->where('session_id', $this->session_id)
    //                    ->with('orderItems.product') 
    //                    ->latest() 
    //                    ->get();

    //     $calls = $this->calls()
    //                   ->where('session_id', $this->session_id)
    //                   ->latest()
    //                   ->get();
        
    //     $history = $orders->concat($calls)->sortByDesc('created_at');

    //     return $history;
    // }
}