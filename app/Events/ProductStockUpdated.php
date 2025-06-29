<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $newStock;
    public $isAvailable;

    /**
     * Create a new event instance.
     *
     * @param int $productId
     * @param int $newStock
     * @param bool $isAvailable
     * @return void
     */
    public function __construct(int $productId, int $newStock, bool $isAvailable)
    {
        $this->productId = $productId;
        $this->newStock = $newStock;
        $this->isAvailable = $isAvailable;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Channel publik, agar semua pengunjung bisa menerima update stok
        return new Channel('products');
    }

    /**
     * Nama event yang akan disiarkan.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'StockUpdated';
    }
}