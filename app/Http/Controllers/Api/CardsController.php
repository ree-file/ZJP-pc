<?php

namespace App\Http\Controllers\Api;

use App\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardsController extends ApiController
{
    public function store(Request $request)
	{
		$this->validate($request, [
			'username' => 'required|max:40',
			'bankname' => 'required|max:255',
			'number' => 'required|max:255',
		]);

		$user = Auth::user();

		$cards_count = Card::where('id', $user->id)->count();
		if ($cards_count >= config('zjp.user.card-max')) {
			return $this->failed('Cards number has reached the limit.');
		}

		$attributes = $request->only(['username', 'bankname', 'number']);
		$card = new Card();
		$card->fill($attributes);
		$card->user_id = $user->id;

		return $this->created();
	}

	public function delete(Request $request)
	{
		$card = Card::find($request->card_id);

		if (! $card) {
			$this->notFound();
		}

		$this->authorize('update', $card);
		$card->delete();

		return $this->message('Deleted.');
	}
}
