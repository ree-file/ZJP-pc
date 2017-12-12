<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractsController extends ApiController
{
	// 升级合约
	public function upgrade(Request $request, Contract $contract)
	{
		$user = Auth::user();
		$pay_active = $request->pay_active;
		$pay_limit = $request->pay_limit;
		$eggs = $request->eggs;
		$user_id = $user->id;
		$contract_id = $contract->id;

		if ($contract->is_finished) {
			return $this->failed('The contract is fisished.');
		}

		// 判断用户资金请求
		if ($pay_limit + $pay_active < $eggs * config('zjp.egg_val')) {
			return $this->failed('Not enough money.');
		}
		if ($pay_limit > $user->money_limit || $pay_active > $user->money_active) {
			return $this->failed('Wallet no enough money.');
		}

		try {
			DB::transaction(function () use ($user_id, $pay_active, $pay_limit, $eggs, $contract_id) {
				$user = User::where('id', $user_id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $pay_active;
				$user->money_limit = $user->money_limit - $pay_limit;
				$user->save();

				$contract = Contract::where('id', $contract_id)->lockForUpdate()->first();
				$contract->eggs = $contract->eggs + $eggs;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed, try later.');
		}

		return $this->success('Payment successful.');
	}
}
