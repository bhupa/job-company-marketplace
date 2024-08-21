<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatEvent implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;

    public function __construct($message)
    {
        Log::info('message trigger');

        $this->message = $message;
        Log::info($this->message);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('message');
        return new Channel('chat');
    }

    public function broadcastWith()
    {
        Log::info('message ddsfs');
        return $this->message;
    }
}
