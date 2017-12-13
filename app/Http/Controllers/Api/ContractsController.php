<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractsController extends ApiController
{
	public function store(Request $request)
	{
		$user = Auth::user();
		$payment = $request->only(['pay_active', 'pay_limit', 'eggs']);
		$nest_id = $request->nest_id;
		$contract_last = Contract::where('nest_id', $nest_id)->orderBy('id', 'desc')->take(1)->first();

		if (! $contract_last->is_finished) {
			return $this->failed('The lastest contract is not finished.');
		}

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment, $user, $nest_id) {
				$user = User::where('id', $user->id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $payment->pay_active;
				$user->money_limit = $user->money_limit - $payment->pay_limit;
				$user->save();

				$contract = new Contract();
				$contract->eggs = $payment->eggs;
				$contract->cycle_date = Carbon::today();
				$contract->nest_id = $nest_id;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed.');
		}

		return $this->message('Payment successful.');
	}

	public function upgrade(Request $request)
	{
		$user = Auth::user();
		$payment = $request->only(['pay_active', 'pay_limit', 'eggs']);
		$contract = Contract::where('id', $request->contract_id)->first();

		if ($contract->is_finished) {
			return $this->failed('The contract is finished.');
		}

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment, $user, $contract) {
				$user = User::where('id', $user->id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $user->pay_active;
				$user->money_limit = $user->money_limit - $user->pay_limit;
				$user->save();

				$contract = Contract::where('id', $contract->id)->lockForUpdate()->first();
				$contract->eggs = $contract->eggs + $payment->eggs;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed.');
		}

		event(new ContractUpgraded($contract, $payment->eggs));

		return $this->success('Payment successful.');
	}

	public function extract(Request $request)
	{
		$user = Auth::user();
		$extract = $request->only(['extract_active', 'extract_limit']);
		$contract = Contract::where('id', $request->contract_id)->first();

		$eggs_hatched = min($contract->eggs, $contract->from_weeks + $contract->from_receivers + $contract->from_community);
		$eggs_active = floor($eggs_hatched * (1 - config('rate.limit')));
		$eggs_limit = floor($eggs_hatched * config('rate.limit'));
		$remaining_active = $eggs_active - $contract->extracted_active;
		$remaining_limit = $eggs_limit - $contract->extracted_limit;

		if ($extract->extract_active > $remaining_active || $extract->extract_limit > $remaining_limit) {
			return $this->failed('Beyond the rest.');
		}

		try {
			DB::transaction(function () use ($extract, $user, $contract) {
				$user = User::where('id', $user->id)->lockForUpdate()->first();
				$user->money_active = $user->money_active + $extract->extract_active * config('egg_val');
				$user->money_limit = $user->money_limit + $extract->extract_limit * config('egg_val');
				$user->save();

				$contract = Contract::where('id', $contract->id)->lockForUpdate()->first();
				$contract->extracted_active = $contract->extracted_active - $extract->extract_active;
				$contract->extracted_limit = $contract->extracted_limit - $extract->extract_limit;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Extract failed.');
		}

		return $this->message('Extract successful.');
	}
}
