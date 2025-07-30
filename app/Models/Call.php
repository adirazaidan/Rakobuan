<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        'call_number',
    ];


    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }

        public function getIsOverdueAttribute(): bool
    {
        $isPending = $this->quantity > $this->quantity_delivered;
        $isLate = Carbon::now()->diffInMinutes($this->created_at) > 1;

        return $isPending && $isLate;
    }

    /**
     * Mendapatkan status panggilan yang sudah diterjemahkan.
     */
    public function getTranslatedStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Belum Diproses',
            'completed' => 'Selesai',
            'handled' => 'Ditangani',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($call) {
            // Format: CALL-YYDDD-NN 
            // Contoh: CALL-25211-01
            $datePart = now()->format('yz');
            $todayCallCount = Call::whereDate('created_at', today())->count();
            $sequence = str_pad($todayCallCount + 1, 2, '0', STR_PAD_LEFT);
            
            $call->call_number = 'CL' . $datePart . $sequence;
        });
    }
}