<?php


namespace App\Handlers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WithdrawalCacheHandler
{
	// 获得今日提现上限
	public function getWithdrawalCeiling($id)
	{
		return Cache::get('withdrawal_ceiling_user_'.$id);
	}

	// 设置今日提现上限
	public function setWithdrawalCeiling($id, $money)
	{
		$expiresAt = Carbon::tomorrow();
		Cache::add('withdrawal_ceiling_user_'.$id, $money, $expiresAt);
	}

	// 获得今日已提现金额
	public function getWithdrawalAlready($id)
	{
		return Cache::get('withdrawal_already_user_'.$id);
	}

	// 设置今日提现金额
	public function setWithdrawalAlready($id, $money)
	{
		$expiresAt = Carbon::tomorrow();
		Cache::put('withdrawal_ceiling_user_'.$id, $money, $expiresAt);
	}
}