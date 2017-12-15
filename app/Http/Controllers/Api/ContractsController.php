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
use Illuminate\Validation\Rule;

class ContractsController extends ApiController
{
	public function store(Request $request)
	{
		$this->validate($request, [
			'nest_id' => 'required',
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in(config('zjp.contract.type'))]
		]);


		$nest = Nest::find($request->nest_id);
		if (! $nest) {
			return $this->notFound();
		}

		$this->authorize('update', $nest);

		$contract = Contract::where('nest_id', $request->nest_id)->latest()->first();
		if (!$contract->is_finished) {
			return $this->failed('The lastest contract is not finished.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs', 'nest_id']), [
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
			$user->money_active = $user->money_active - $payment->pay_active;
			$user->money_limit = $user->money_limit - $payment->pay_limit;
			$user->save();

			$contract = new Contract();
			$contract->eggs = $payment->eggs;
			$contract->cycle_date = Carbon::today();
			$contract->nest_id = $payment->nest_id;
			$contract->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	public function upgrade(Request $request)
	{
		$this->validate($request, [
			'contract_id' => 'required',
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => 'required|integer'
		]);


		$contract = Contract::where('id', $request->contract_id)->first();

		if (! $contract) {
			return $this->notFound();
		}
		if (!in_array((int) ($request->eggs + $contract->eggs), config('zjp.contract.type'))) {
			return $this->message('Eggs count wrong.');
		}

		$this->authorize('update', $contract);

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs', 'contract_id']), [
			'price' => $request->eggs * config('zjp.contract.egg.val')
		]);

		DB::beginTransaction();
		try {
			$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->with('nest.parent.parent')->first();
			if ($contract->is_finished) {
				throw new \Exception('The contract is finished.');
			}
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

			$contract->eggs = $contract->eggs + $payment['eggs'];
			$contract->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		event(new ContractUpgraded($contract, $payment['eggs']));

		return $this->message('Upgraded.');
	}

	public function extract(Request $request)
	{
		$this->validate($request, [
			'contract_id' => 'required',
			'extract_active' => 'required|integer|min:0',
			'extract_limit' => 'required|integer|min:0'
		]);

		$user = Auth::user();

		$contract = Contract::find($request->contract_id);
		if (! $contract) {
			return $this->notFound();
		}

		$this->authorize('update', $contract);

		$payment = $request->only(['extract_active', 'extract_limit', 'contract_id']);

		DB::beginTransaction();
		try {
			$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->first();

			$eggs_hatched = min($contract->eggs, $contract->from_weeks + $contract->from_receivers + $contract->from_community);
			$remaining_active = floor($eggs_hatched * (1 - config('zjp.rate.limit'))) - $contract->extracted_active;
			$remaining_limit = floor($eggs_hatched * config('zjp.rate.limit')) - $contract->extracted_limit;

			if ($payment['extract_active'] > $remaining_active || $payment['extract_limit'] > $remaining_limit) {
				throw new \Exception('Beyond the rest.');
			}

			$contract->extracted_active = $contract->extracted_active + $payment['extract_active'];
			$contract->extracted_limit = $contract->extracted_limit + $payment['extract_limit'];
			$contract->save();

			$user = User::where('id', $user->id)->lockForUpdate()->first();
			$user->money_active = $user->money_active + $payment['extract_active'] * config('zjp.contract.egg.val');
			$user->money_limit = $user->money_limit + $payment['extract_limit'] * config('zjp.contract.egg.val');
			$user->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->message('Extracted.');
	}
}
