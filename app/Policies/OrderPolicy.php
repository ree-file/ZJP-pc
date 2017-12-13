<?php

namespace App\Policies;

use App\Order;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

	public function update(User $user, Order $order)
	{
		return $user->id == $order->seller_id;
	}
}
