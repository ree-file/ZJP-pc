<?php

namespace App\Policies;

use App\Contract;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
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

    public function update(User $user, Contract $contract)
	{
		return $user->id == $contract->nest->user_id;
	}
}
