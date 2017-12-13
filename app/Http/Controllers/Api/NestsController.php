<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Nest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NestsController extends ApiController
{
	public function store(Request $request)
	{
		$user = Auth::user();
		$payment = array_merge($request->only(['name', 'inviter_id', 'parent_id', 'community', 'pay_active', 'pay_limit', 'eggs']), [
			'user_id' => $user->id,
			'price' => $request->eggs * config('zjp.egg.val')
		]);

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment){
				$nest = new Nest();
				$nest->name = $payment->name;
				$nest->inviter_id = $payment->inviter_id;
				$nest->parent_id = $payment->parent_id;
				$nest->community = $payment->community;
				$nest->user_id = $payment->user_id;
				$nest->save();

				$user = User::where('id', $payment->user_id)->lockForUpdate()->first();
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

	public function nest(Request $request)
	{
		$nest = Nest::where('name', $request->nest_name)->with('children')->first();
		return $this->success($nest->toArray());
	}
}
