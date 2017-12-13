<?php

namespace App\Http\Controllers\Api;

use App\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuppliesController extends ApiController
{
    public function store(Request $request)
	{
		$supply = new Supply();
		$supply->user_id = Auth::id();
		$supply->money = $request->money;
		$supply->message = $request->message;
		$supply->save();

		return $this->created();
	}
}
