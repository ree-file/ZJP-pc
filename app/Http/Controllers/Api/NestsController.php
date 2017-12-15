<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Nest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NestsController extends ApiController
{
	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|unique:nests|max:100',
			'inviter_id' => 'required|integer',
			'parent_id' => 'required|integer',
			'community' => ['required', Rule::in(['A', 'B', 'C'])],
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in(config('zjp.contract.type'))]
		]);

		$user = Auth::user();
		$payment = array_merge($request->only(['name', 'inviter_id', 'parent_id', 'community', 'pay_active', 'pay_limit', 'eggs']), [
			'price' => $request->eggs * config('zjp.contract.egg.val')
		]);


		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
				throw new \Exception('Not enough money.');
			}
			if ($payment['pay_active'] > $user->money_active || $payment['pay_limit'] > $user->money_limit) {
				throw new \Exception('Wallet no enough money.');
			}
			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_limit = $user->money_limit - $payment['pay_limit'];
			$user->save();

			$nest = new Nest();
			$nest->name = $payment['name'];
			$nest->inviter_id = $payment['inviter_id'];
			$nest->parent_id = $payment['parent_id'];
			$nest->community = $payment['community'];
			$nest->user_id = $user->id;
			$nest->save();

			$contract = new Contract();
			$contract->eggs = $payment['eggs'];
			$contract->nest_id = $nest->id;
			$contract->cycle_date = Carbon::today();
			$contract->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	public function nest(Request $request)
	{
		$nest = Nest::where('name', $request->name)->with('children')->first();
		if (! $nest) {
			return $this->notFound();
		}

		return $this->success($nest->toArray());
	}
}
