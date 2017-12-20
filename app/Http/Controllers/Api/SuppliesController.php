<?php

namespace App\Http\Controllers\Api;

use App\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuppliesController extends ApiController
{
    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'type' => ['required', Rule::in(['save', 'get'])],
			'card_number' => 'required',
			'money' => 'required|numeric|min:0',
			'message' => 'required|max:255'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}


		$supply = new Supply();
		$supply->user_id = Auth::id();
		$supply->type = $request->type;
		$supply->card_number = $request->card_number;
		$supply->money = $request->money;
		$supply->message = $request->message;
		$supply->save();

		return $this->created();
	}
}
