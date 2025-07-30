<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call; // Gunakan public property untuk data

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Call $call
     * @return void
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
        // Siarkan event ini ke channel spesifik sesi pelanggan
        return [
            new Channel('customer-session.' . $this->call->session_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        // Tentukan nama event yang akan didengarkan di frontend
        return 'call-received';
    }
}