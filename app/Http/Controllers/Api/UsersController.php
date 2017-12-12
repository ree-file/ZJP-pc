<?php

namespace App\Http\Controllers\Api;

use App\Nest;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
	use ApiResponse;

    public function getNests(User $user)
	{
		$nests = Nest::where('id', $user->id)->paginate(10);

		return $this->success($nests);
	}
}
