<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Nest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Nest as NestResource;

class UsersController extends ApiController
{
    public function user(Request $request)
	{
		$user = $request->user();
		return $this->success($user);
	}

	public function cards()
	{
		$user = Auth::user();
		$cards = Card::where('id', $user->id)->get();

		return $this->success($cards->toArray());
	}

	public function nests()
	{
		$user = Auth::user();
		$nests= Nest::where('user_id', $user->id)->get();

		return $this->success($nests);
	}

	public function nest(Request $request)
	{
		$nest = Nest::where('id', $request->nest_id)->with('inviter', 'receivers', 'parent', 'children.children')->first();
		return $this->success(new NestResource($nest));
	}
}
