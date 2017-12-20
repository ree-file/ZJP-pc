<?php

namespace App\Http\Controllers\Api;

use App\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardsController extends ApiController
{
    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|max:40',
			'bankname' => 'required|max:255',
			'number' => 'required|max:255',
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();

		// 判别用户的银行卡数量是否超过限制
		$cards_count = Card::where('id', $user->id)->count();
		if ($cards_count >= (int) config('zjp.USER_MAXIMUM_CARDS')) {
			return $this->failed('Cards number has reached the limit.');
		}

		$attributes = $request->only(['username', 'bankname', 'number']);
		$card = new Card();
		$card->fill($attributes);
		$card->user_id = $user->id;
		$card->save();

		return $this->created();
	}

	public function destroy(Request $request, Card $card)
	{
		if (! $card) {
			return $this->notFound();
		}

		$this->authorize('update', $card);
		$card->delete();

		return $this->deleted();
	}
}
