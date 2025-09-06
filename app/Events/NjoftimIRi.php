<?php

namespace App\Events;

use App\Models\Njoftimet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NjoftimIRi implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $njoftim;

    /**
     * Create a new event instance.
     */
    public function __construct(Njoftimet $njoftim)
    {
        $this->njoftim = $njoftim;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('njoftimet.' . $this->njoftim->perdorues_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'njoftim_id' => $this->njoftim->njoftim_id,
            'mesazhi' => $this->njoftim->mesazhi,
            'lloji_njoftimit' => $this->njoftim->lloji_njoftimit,
            'data_krijimit' => $this->njoftim->data_krijimit,
            'data_krijimit_human' => $this->njoftim->data_krijimit->diffForHumans(),
            'lexuar' => $this->njoftim->lexuar,
        ];
    }
}
