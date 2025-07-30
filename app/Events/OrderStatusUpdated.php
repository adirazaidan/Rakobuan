<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel; 
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Tentukan channel siaran.
     */
    public function broadcastOn(): array
    {
        // Kirim ke channel publik yang namanya unik berdasarkan ID Sesi order
        return [
            new Channel('order-status.' . $this->order->session_id),
        ];
    }
}