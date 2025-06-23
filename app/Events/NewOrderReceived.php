<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- Pastikan ini ada
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcast // <-- IMPLEMENTASI INTERFACE
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Mendapatkan channel tempat event akan disiarkan.
     */
    public function broadcastOn(): array
    {
        // Kita gunakan PrivateChannel agar hanya admin yang bisa mendengar
        return [
            new PrivateChannel('orders'),
        ];
    }
}