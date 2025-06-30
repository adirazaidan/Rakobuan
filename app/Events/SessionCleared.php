<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionCleared implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionId;

    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }
    public function broadcastOn(): array
    {
        return [
            new Channel('customer-logout.' . $this->sessionId),
        ];
    }
}