<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\Nest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractsController extends ApiController
{
	public function store(Request $request)
	{
		$nest = Nest::find($request->nest_id);
		$this->authorize('update', $nest);

		$contract = Contract::where('nest_id', $request->nest_id)->latest()->first();
		if (!$contract->is_finished) {
			return $this->failed('The lastest contract is not finished.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs', 'nest_id']), [
			'user_id' => $user->id,
			'price' => $request->eggs * config('zjp.egg.val')
		]);

		try {
			DB::transaction(function () use ($payment) {
				$user = User::where('id', $payment['user_id'])->lockForUpdate()->first();
				if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
					DB::rollBack();
					return $this->failed('Not enough money.');
				}
				if ($payment['pay_active'] > $user->money_limit || $payment['pay_limit'] > $user->money_active) {
					DB::rollBack();
					return $this->failed('Wallet no enough money.');
				}
				$user->money_active = $user->money_active - $payment->pay_active;
				$user->money_limit = $user->money_limit - $payment->pay_limit;
				$user->save();

				$contract = new Contract();
				$contract->eggs = $payment->eggs;
				$contract->cycle_date = Carbon::today();
				$contract->nest_id = $payment->nest_id;
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed.');
		}

		return $this->message('Payment successful.');
	}

	public function upgrade(Request $request)
	{
		$contract = Contract::where('id', $request->contract_id)->with('nest')->first();
		$this->authorize('update', $contract);

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs', 'contract_id']), [
			'user_id' => $user->id,
			'price' => $request->eggs * config('zjp.egg.val')
		]);

		if ($contract->is_finished) {
			return $this->failed('The contract is finished.');
		}

		try {
			DB::transaction(function () use ($payment) {
				$user = User::where('id', $payment['user_id'])->lockForUpdate()->first();
				if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
					DB::rollBack();
					return $this->failed('Not enough money.');
				}
				if ($payment['pay_active'] > $user->money_limit || $payment['pay_limit'] > $user->money_active) {
					DB::rollBack();
					return $this->failed('Wallet no enough money.');
				}
				$user->money_active = $user->money_active - $user->pay_active;
				$user->money_limit = $user->money_limit - $user->pay_limit;
				$user->save();

				$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->first();
				$contract->eggs = $contract->eggs + $payment['eggs'];
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed.');
		}

		event(new ContractUpgraded($contract, $payment['eggs']));

		return $this->success('Payment successful.');
	}

	public function extract(Request $request)
	{
		$contract = Contract::find($request->contract_id);
		$this->authorize('update', $contract);

		$payment = $request->only(['extract_active', 'extract_limit', 'contract_id']);

		try {
			DB::transaction(function () use ($payment) {
				$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->first();

				$eggs_hatched = min($contract->eggs, $contract->from_weeks + $contract->from_receivers + $contract->from_community);
				$remaining_active = floor($eggs_hatched * (1 - config('rate.limit'))) - $contract->extracted_active;
				$remaining_limit = floor($eggs_hatched * config('rate.limit')) - $contract->extracted_limit;

				if ($payment['extract_active'] > $remaining_active || $payment['extract_limit'] > $remaining_limit) {
					DB::rollBack();
					return $this->failed('Beyond the rest.');
				}

				$contract->extracted_active = $contract->extracted_active - $payment['extract_active'];
				$contract->extracted_limit = $contract->extracted_limit - $payment['extract_limit'];
				$contract->save();

				$user = User::where('id', $payment['user_id'])->lockForUpdate()->first();
				$user->money_active = $user->money_active + $payment['extract_active'] * config('egg.val');
				$user->money_limit = $user->money_limit + $payment['extract_limit'] * config('egg.val');
				$user->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Extract failed.');
		}

		return $this->message('Extract successful.');
	}
}
