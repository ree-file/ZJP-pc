<?php

namespace App\Http\Controllers\Api;

use App\Supply;
use App\Traits\ImageUploadHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuppliesController extends ApiController
{
	use ImageUploadHandler;

    public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'type' => ['required', Rule::in(['save', 'get'])],
			'card_number' => 'required',
			'money' => 'required|numeric|min:0',
			'message' => 'required|max:255',
			'image' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200'
		]);

		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$supply = new Supply();
		if ($request->type == 'save') {
			if ($request->image) {
				$result = $this->save($request->image, 'supplies', Auth::id(), 800);
				if ($result) {
					$supply->image = $result['path'];
				}
			}
		}

		$supply->user_id = Auth::id();
		$supply->type = $request->type;
		$supply->card_number = $request->card_number;
		$supply->money = $request->money;
		$supply->message = $request->message;
		$supply->save();

		return $this->created();
	}
}
