<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $tableId;
    public function __construct(int $tableId) { $this->tableId = $tableId; }
    public function broadcastOn(): array { return [ new PrivateChannel('layout-tables') ]; }
    public function broadcastAs(): string { return 'TableStatusUpdated'; }
}