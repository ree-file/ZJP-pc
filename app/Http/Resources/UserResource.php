<?php

namespace App\Http\Resources;

use App\Handlers\WithdrawalCacheHandler;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		$data = [
			'id'                => $this->id,
			'email'             => $this->email,
			'money_active'      => $this->money_active,
			'money_limit'       => $this->money_limit,
			'money_withdrawal'  => $this->money_withdrawal,
			'coins'				=> $this->coins,
			'is_freezed'        => $this->is_freezed,
			'created_at'		=> date($this->created_at),
			'has_security_code' => $this->security_code != null ? true : false
		];

		// 如果请求详细信息
		if ($request->tab == 'detail') {

			$cardsCount = $this->cards->count();
			$nestsCount = $this->nests->count();

			$rechargeMoney = $this->rechargeApplications->where('status', 'accepted')->sum('money');
			$withdrawalMoney = $this->withdrawalApplications->where('status', 'accepted')->sum('money');

			$transferPayed = $this->transferRecordsOfPaying->sum('money');
			$transferReceived = $this->transferRecordsOfReceiving->sum('money');

			$incomeMoneyActive = $this->incomeRecords->sum('money_active');
			$incomeMoneyLimit = $this->incomeRecords->sum('money_limit');
			$incomeCoins = $this->incomeRecords->sum('coins');

			$today = Carbon::today();
			$incomeToday = $this->incomeRecords->filter(function ($value, $key) use ($today) {
					return $today->lt($value->created_at);
				});

			$incomeMoneyActiveToday = $incomeToday->sum('money_active');
			$incomeMoneyLimitToday = $incomeToday->sum('money_limit');
			$incomeCoinsToday = $incomeToday->sum('coins');

			$investEggs = $this->investRecords->sum('eggs');
			$investMoney = $this->investRecords->sum('money');
			$investVal = $this->investRecords->sum('eggs') * config('website.EGG_VAL');

			$transactionPayed = $this->transactionRecordsOfSelling->sum('price');
			$transactionIncome = $this->transactionRecordsOfBuying->sum('income');

			$analyse = [
				'cards_count' => $cardsCount,
				'nests_count' => $nestsCount,
				'recharge_money' => $rechargeMoney,
				'withdrawal_money' => $withdrawalMoney,
				'transfer_payed' => $transferPayed,
				'transfer_received' => $transferReceived,
				'income_money_active' => $incomeMoneyActive,
				'income_money_limit' => $incomeMoneyLimit,
				'income_coins' => $incomeCoins,
				'income_money_active_today' => $incomeMoneyActiveToday,
				'income_money_limit_today' => $incomeMoneyLimitToday,
				'income_coins_today' => $incomeCoinsToday,
				'invest_eggs' => $investEggs,
				'invest_money' => $investMoney,
				'transaction_payed' => $transactionPayed,
				'transaction_income' => $transactionIncome
			];

			$data['analyse'] = $analyse;
		}

		return $data;
    }
}
