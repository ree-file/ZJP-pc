<?php

namespace App\Http\Controllers\Api;

use App\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardsController extends ApiController
{
    public function store(Request $request)
	{
		$user = Auth::user();

		$cards_count = Card::where('id', $user->id)->count();
		if ($cards_count >= config('zjp.card.max')) {
			$this->failed('Cards number has reached the limit.');
		}

		$attributes = $request->only(['username', 'bankname', 'number']);
		$card = new Card();
		$card->fill($attributes);
		$card->user_id = $user->id;

		return $this->created();
	}
}
