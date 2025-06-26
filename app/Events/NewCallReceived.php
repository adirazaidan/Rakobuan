<?php

namespace App\Events;

use App\Models\Call; // Pastikan menggunakan model Call
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

    public $call;

    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('calls'),
        ];
    }

    /**
     * Menentukan nama siaran untuk event ini.
     * INI JUGA PERBAIKANNYA.
     */
    public function broadcastAs(): string
    {
        return 'NewCallReceived';
    }
}