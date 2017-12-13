<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Nest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Nest as NestResource;

class NestsController extends ApiController
{
	public function store(Request $request)
	{
		$user = Auth::user();
		$nest_attr = $request->only(['name', 'inviter_id', 'parent_id', 'community']);
		$payment = $request->only(['pay_active', 'pay_limit', 'eggs']);

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment, $user, $nest_attr){
				$nest = new Nest();
				$nest->fill($nest_attr);
				$nest->user_id = $user->id;
				$nest->save();

				$user = User::where('id', $user->id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $payment->pay_active;
				$user->money_limit = $user->money_limit - $payment->pay_limit;
				$user->save();

				$contract = new Contract();
				$contract->eggs = $payment->eggs;
				$contract->nest_id = $nest->id;
				$contract->cycle_date = Carbon::today();
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Create failed.');
		}

		return $this->created();
	}
}
