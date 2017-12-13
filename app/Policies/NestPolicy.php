<?php

namespace App\Policies;

use App\Nest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NestPolicy
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

    public function update(User $user, Nest $nest)
	{
		return $user->id == $nest->user_id;
	}
}
