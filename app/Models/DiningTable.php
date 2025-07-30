<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Events\TableStatusUpdated;
use App\Events\AvailableTablesUpdated;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'notes', 'is_locked', 'session_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function activeOrders()
    {
        return $this->hasMany(Order::class)->whereNotIn('status', ['completed', 'cancelled'])->latest();
    }

    public function activeCalls()
    {
        return $this->hasMany(Call::class)->where('status', 'pending'); 
    }

    /**
     * ACCESSOR BARU: Mengambil SEMUA pesanan yang selesai
     * untuk sesi yang sedang aktif di meja ini.
     */
    public function getCompletedOrdersForCurrentSessionAttribute()
    {
        if (!$this->session_id) {
            return collect(); // Kembalikan koleksi kosong jika tidak ada sesi
        }
        return $this->orders()
                    ->where('status', 'completed')
                    ->where('session_id', $this->session_id)
                    ->orderBy('created_at', 'asc') // Urutkan dari yang paling awal
                    ->get();
    }

    /**
     * ACCESSOR BARU: Mengambil SEMUA panggilan (baik aktif maupun selesai)
     * untuk sesi yang sedang aktif di meja ini.
     */
    public function getCallsForCurrentSessionAttribute()
    {
        if (!$this->session_id) {
            return collect(); // Kembalikan koleksi kosong jika tidak ada sesi
        }
        return $this->calls()
                    ->where('session_id', $this->session_id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

     /**
     * Method terpusat untuk membersihkan sesi dari sebuah meja.
     *
     * @param string|null $sessionId ID sesi yang akan dibersihkan.
     * @return void
     */
    public static function clearSessionFor(?string $sessionId): void
    {
        if (!$sessionId) {
            return;
        }

        $table = self::where('session_id', $sessionId)->first();

        if ($table) {
            $table->update(['session_id' => null]);
            TableStatusUpdated::dispatch($table->id);
            AvailableTablesUpdated::dispatch();
        }
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}