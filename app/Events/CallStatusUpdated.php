<?php

namespace App\Events;

use App\Models\Call; // Import model Call
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callNumber;
    public $newStatus;
    public $translatedStatus; 
    public $sessionId; 

    /**
     * Create a new event instance.
     */
    public function __construct(Call $call)
    {
        $this->callNumber = $call->call_number;
        $this->newStatus = $call->status;
        $this->translatedStatus = $call->translated_status ?? ($call->status == 'pending' ? 'Menunggu' : ($call->status == 'handled' ? 'Ditangani' : 'Selesai'));
        $this->sessionId = $call->session_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Event ini akan didengarkan di channel publik yang spesifik untuk sesi_id pelanggan
        // Ini akan memastikan hanya pelanggan di sesi yang relevan yang menerima update
        return [
            new Channel('customer-session.' . $this->sessionId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'call-status-updated';
    }
}