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

class ContractUpgraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contract;
    public $eggs;

    public function __construct(Contract $contract, $eggs)
    {
        $this->contract = $contract;
        $this->eggs = $eggs;
    }
}
