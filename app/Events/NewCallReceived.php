<?php

namespace App\Events;

use App\Models\Call; // <-- Gunakan model Call
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCallReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call; // Kita akan mengirim data panggilan

    /**
     * Create a new event instance.
     */
    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Kita siarkan di channel privat baru bernama 'calls'
        return [
            new PrivateChannel('calls'),
        ];
    }
}