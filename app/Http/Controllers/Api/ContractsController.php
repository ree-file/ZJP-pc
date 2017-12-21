<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\Nest;
use App\NestRecord;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContractsController extends ApiController
{
	public function extract(Request $request, Contract $contract)
	{
		$validator = Validator::make($request->all(), [
			'extract_active' => 'required|integer|min:0',
			'extract_limit' => 'required|integer|min:0'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		if (! $contract) {
			return $this->notFound();
		}

		$this->authorize('update', $contract);

		$user = Auth::user();
		$payment = array_merge($request->only(['extract_active', 'extract_limit']), [
			'contract_id' => $contract->id
		]);

		DB::beginTransaction();
		try {
			$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->first();

			// 已经孵化的蛋数，最大为购入合约的三倍，或为周获取、邀请获取、社区获取的总和。
			$eggs_hatched = min($contract->eggs * (float) config('zjp.CONTRACT_PROFITE_RATE'), $contract->from_weeks + $contract->from_receivers + $contract->from_community);
			// 剩余可提取为活动资金的蛋数
			$remaining_active = floor($eggs_hatched * (1 - (float) config('zjp.CONTRACT_EXTRACT_LIMTE_RATE'))) - $contract->extracted_active;
			// 剩余可提取为限制资金的蛋数
			$remaining_limit = floor($eggs_hatched * (float) config('zjp.CONTRACT_EXTRACT_LIMTE_RATE')) - $contract->extracted_limit;

			if ($payment['extract_active'] > $remaining_active || $payment['extract_limit'] > $remaining_limit) {
				throw new \Exception('Beyond the rest.');
			}

			$contract->extracted_active = $contract->extracted_active + $payment['extract_active'];
			$contract->extracted_limit = $contract->extracted_limit + $payment['extract_limit'];
			$contract->save();

			$user = User::where('id', $user->id)->lockForUpdate()->first();
			$user->money_active = $user->money_active + $payment['extract_active'] * (int) config('zjp.EGG_VAL');
			$user->money_limit = $user->money_limit + $payment['extract_limit'] * (int) config('zjp.EGG_VAL');
			$user->save();


			$nest_record = new NestRecord();
			$nest_record->nest_id = $contract->nest_id;
			$nest_record->contract_id = $contract->id;
			$nest_record->user_id = $user->id;
			$nest_record->type = 'extract';
			$nest_record->eggs = $payment['extract_active'] + $payment['extract_limit'];
			$nest_record->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->message('Extracted.');
	}
}
