<?php

namespace App\Policies;

use App\Card;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardPolicy
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

	public function update(User $user, Card $card)
	{
		return $user->id == $card->user_id;
	}
}
