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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContractsController extends ApiController
{
	protected $cache_extrac_name = 'today_contract_extract_';

	public function extract(Request $request, Contract $contract)
	{
		$validator = Validator::make($request->all(), [
			'extract' => 'required|integer|min:0'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		if (!$contract) {
			return $this->notFound();
		}

		$this->authorize('update', $contract);

		$user = Auth::user();
		$payment = array_merge($request->only(['extract']), [
			'contract_id' => $contract->id
		]);

		// 缓存检测是否达到今日提取最大值
		$today_extracted = Cache::get($this->cache_extrac_name.$contract->id) ? (int) Cache::get($this->cache_extrac_name.$contract->id) : 0;
		if (($today_extracted + $payment['extract']) >= $contract->eggs * (float) config('zjp.CONTRACT_DAILY_EXTRACT_RATE') * (float) config('zjp.CONTRACT_PROFITE_RATE')) {
			return $this->failed('Extract today to reach the maximum.');
		}


		DB::beginTransaction();
		try {
			$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->first();

			// 已经孵化的蛋数，最大为购入合约的三倍，或为周获取、邀请获取、社区获取的总和。
			$eggs_hatched = min($contract->eggs * (float)config('zjp.CONTRACT_PROFITE_RATE'), $contract->from_weeks + $contract->from_receivers + $contract->from_community);
			// 剩余可提取为活动资金的蛋数
			$remaining = floor($eggs_hatched) - $contract->extracted;

			if ($payment['extract'] > $remaining) {
				throw new \Exception('Beyond the rest.');
			}

			$contract->extracted = $contract->extracted + $payment['extract'];
			$contract->save();

			$user = User::where('id', $user->id)->lockForUpdate()->first();
			$user->money_active = $user->money_active + $payment['extract'] * config('zjp.EGG_VAL') * (1 - (float)config('zjp.CONTRACT_EXTRACT_LIMTE_RATE'));
			$user->money_limit = $user->money_limit + $payment['extract_limit'] * config('zjp.EGG_VAL') * (float)config('zjp.CONTRACT_EXTRACT_LIMTE_RATE');
			$user->save();


			$nest_record = new NestRecord();
			$nest_record->nest_id = $contract->nest_id;
			$nest_record->contract_id = $contract->id;
			$nest_record->user_id = $user->id;
			$nest_record->type = 'extract';
			$nest_record->eggs = $payment['extract'];
			$nest_record->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();

			return $this->failed($e->getMessage());
		}

		if (Cache::get($this->cache_extrac_name.$contract->id)) {
			Cache::increment($this->cache_extrac_name.$contract->id, $payment['extract']);
		} else {
			Cache::put($this->cache_extrac_name.$contract->id, $payment['extract'], 1440);
		}

		return $this->message('Extracted.');
	}
}
