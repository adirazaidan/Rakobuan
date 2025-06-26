<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'is_locked',
        'location',
        'session_id',
    ];

        public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class)->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function latestCompletedOrder()
    {
        return $this->hasOne(Order::class)->where('status', 'completed')->latestOfMany();
    }

    public function activeCalls()
    {
        return $this->hasMany(Call::class)->where('status', '!=', 'completed');
    }
}