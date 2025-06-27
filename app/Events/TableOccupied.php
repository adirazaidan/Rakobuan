<?php

namespace App\Events;

use App\Models\DiningTable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableOccupied implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DiningTable $table;

    /**
     * Create a new event instance.
     */
    public function __construct(DiningTable $table)
    {
        $this->table = $table;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Kirim sinyal ke channel admin
        return [
            new PrivateChannel('layout-tables'),
        ];
    }

    /**
     * Beri nama panggilan yang bersih untuk event ini
     */
    public function broadcastAs(): string
    {
        return 'TableOccupied';
    }
}