<?php

namespace App\Http\Controllers\Api;

use App\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SuppliesController extends ApiController
{
	/*
	 * 提交表单操作信息
	 * */

    public function store(Request $request)
	{
		$this->validate($request, [
			'type' => ['required', Rule::in(['save', 'get'])],
			'money' => 'required|numeric|min:0',
			'message' => 'required|max:255'
		]);

		$supply = new Supply();
		$supply->user_id = Auth::id();
		$supply->type = $request->type;
		$supply->card_number = $request->card_number;
		$supply->money = $request->money;
		$supply->message = $request->message;
		$supply->save();

		return $this->created();
	}

	public function activeToMarket(Request $request)
	{
		$this->validate($request, [
			'pay_active' => 'required|numeric|min:0',
		]);

		$user = Auth::user();
		$payment = $request->only(['pay_active']);
		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_active'] > $user->money_active) {
				throw new \Exception('Wallet no enough money.');
			}
			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_market = $user->money_market + $payment['pay_active'];
			$user->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}
		return $this->message('Converted');
	}

	public function marketToActive(Request $request)
	{
		$this->validate($request, [
			'pay_active' => 'required|numeric|min:0',
		]);

		$user = Auth::user();
		$payment = $request->only(['pay_market']);
		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_market'] > $user->money_market) {
				throw new \Exception('Wallet no enough money.');
			}
			$user->money_active = $user->money_active + $payment['pay_market'] * (1 - config('zjp.user.tax.market-to-active'));
			$user->money_market = $user->money_market - $payment['pay_market'];
			$user->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}
		return $this->message('Converted');
	}
}
