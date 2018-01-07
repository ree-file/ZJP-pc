<?php

namespace App\Http\Resources;

use App\Contract;
use App\Nest;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class NestResource extends Resource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		// 找到相对深度为20的所有下级
		$descendants = Nest::withDepth()
			->having('depth', '<=', $this->depth + 20)
			->descendantsOf($this->id);

		// 下级数量
		$depth1Count = $descendants->where('depth', $this->depth + 1)->count();
		$depth2Count = $descendants->where('depth', $this->depth + 2)->count();
		$depth3Count = $descendants->where('depth', $this->depth + 3)->count();
		$descendantsCount = $descendants->count();

		$analyse = [
			'depth1_count' => $depth1Count,
			'depth2_count' => $depth2Count,
			'depth3_count' => $depth3Count,
			'descendants_count' => $descendantsCount,
			'highest_price' => $this->transactionRecords->max('price')
		];

		// 如果是猫窝窝主查看，并要求提供更详细的统计信息
		if (Auth::id() == $this->user_id && $request->tab == 'detail') {
			// 合约统计信息
			$contractsCount = $this->contracts->count();
			$contractsEggsSum = $this->contracts->sum('eggs');
			$contractsVal = $contractsEggsSum * config('website.EGG_VAL');
			$contractsHatchesSum = $this->contracts->sum('hatches');

			// 窝主投资统计信息
			$investEggsSum = $this->investRecords->where('user_id', Auth::id())->sum('eggs');
			$investVal = $investEggsSum * config('website.EGG_VAL');

			// 窝主收入统计信息
			$incomeMoneyActive = $this->incomeRecords->where('user_id', Auth::id())->sum('money_active');
			$incomeMoneyLimit = $this->incomeRecords->where('user_id', Auth::id())->sum('money_limit');
			$incomeCoins = $this->incomeRecords->where('user_id', Auth::id())->sum('coins');

			$data = [
				'contracts_count' => $contractsCount,
				'contracts_eggs_sum' => $contractsEggsSum,
				'contracts_val' => $contractsVal,
				'contracts_hatches_sum' => $contractsHatchesSum,
				'invest_eggs_sum' => $investEggsSum,
				'invest_val' => $investVal,
				'income_money_active' => $incomeMoneyActive,
				'income_money_limit' => $incomeMoneyLimit,
				'income_coins' => $incomeCoins,
			];

			$analyse = array_merge($analyse, $data);
		}

		return [
			'id'   => $this->id,
			'name' => $this->name,
			'user_id' => $this->user_id,
			'user' => $this->user,
			'created_at' => date($this->created_at),
			'is_selling' => $this->is_selling,
			'price' => $this->price,
 			'parent' => $this->parent,
			'analyse' => $analyse,
			'is_owner' => $this->user_id == Auth::id() ? true : false,
		];
	}
}
