<?php

namespace App\Events;

use App\Contract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NestUpgraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public $contract;
	public $eggs;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contract $contract, $eggs)
    {
		$this->contract = $contract;
		$this->eggs = $eggs;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
