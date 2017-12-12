<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Nest;
use App\Repositories\NestRepository;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NestsController extends Controller
{
	use ApiResponse;

	public function getNest(Nest $nest)
	{
		return $this->success($nest->toArray());
	}

	public function newContract(Request $request, Nest $nest)
	{
		$user = Auth::user();
		$nest_id = $nest->id;
		$contract_last = Contract::where('nest_id', $nest_id)->orderBy('id', 'desc')->take(1)->first();
		$pay_active = $request->pay_active;
		$pay_limit = $request->pay_limit;
		$eggs = $request->eggs;
		$user_id = $user->id;

		if (! $contract_last->is_finished) {
			return $this->failed('Last contract is not fisished.');
		}

		// 判断用户资金请求
		if ($pay_limit + $pay_active < $eggs * config('zjp.egg_val')) {
			return $this->failed('Not enough money.');
		}
		if ($pay_limit > $user->money_limit || $pay_active > $user->money_active) {
			return $this->failed('Wallet no enough money.');
		}


		try {
			DB::transaction(function () use ($user_id, $pay_active, $pay_limit, $eggs, $nest_id) {
				$user = User::where('id', $user_id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $pay_active;
				$user->money_limit = $user->money_limit - $pay_limit;
				$user->save();

				$contract = new Contract();
				$contract->eggs = $eggs;
				$contract->nest_id = $nest_id;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed, try later.');
		}

		return $this->message('Payment successful.');
	}

	public function getContracts(Nest $nest)
	{
		$contracts = Contract::where('id', $nest->id)->orderBy('id', 'desc')->paginate(10);

		return $this->success($contracts);
	}

	public function getLastContract(Nest $nest)
	{
		$contract = Contract::where('id', $nest->id)->orderBy('id', 'desc')->first();

		return $this->success($contract);
	}
}
