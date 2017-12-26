<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Http\Resources\UserResource;
use App\Mail\ResetSecurityCodeMail;
use App\Nest;
use App\Order;
use App\Supply;
use App\Traits\CodeCacheHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PrivateController extends ApiController
{
	use CodeCacheHelper;
	// 返回登录用户个人信息
	public function user()
	{
		$user = Auth::user();
		return $this->success(new UserResource($user));
	}
	// 返回私有资源
	public function cards()
	{
		$user = Auth::user();
		$cards = Card::where('user_id', $user->id)->get();

		return $this->success($cards->toArray());
	}

	public function nests()
	{
		$user = Auth::user();
		$nests = Nest::where('user_id', $user->id)->with('contracts')->get();

		return $this->success($nests);
	}

	public function orders()
	{
		$user = Auth::user();
		$orders = Order::where('seller_id', $user->id)->orWhere('buyer_id', $user->id)->with('nest')->get();
		return $this->success($orders);
	}

	public function supplies()
	{
		$user = Auth::user();
		$supplies = Supply::where('user_id', $user->id)->get();
		return $this->success($supplies);
	}
	// 个人钱包操作
	public function transferMoney(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'pay' => 'required|numeric|min:0',
			'type' => ['required', Rule::in([
				'active-to-market',
				'market-to-active'
			])]
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();
		$payment = $request->only(['pay', 'type']);
		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['type'] == 'active-to-market') {
				if ($payment['pay'] > $user->money_active) {
					throw new \Exception('Wallet no enough money.');
				}
				$user->money_active = $user->money_active - $payment['pay'];
				$user->money_market = $user->money_market + $payment['pay'];
				$user->save();
			}
			if ($payment['type'] == 'market-to-active') {
				if ($payment['pay'] > $user->money_market) {
					throw new \Exception('Wallet no enough money.');
				}
				$user->money_active = $user->money_active + $payment['pay'] * (1 - (float) config('zjp.MONEY_MARKET_TO_ACTIVE_TAX_RATE'));
				$user->money_market = $user->money_market - $payment['pay'];
				$user->save();
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}
		return $this->message('Transfered.');
	}
	// 修改密码
	public function changePassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => 'required|max:255',
			'new_password' => 'required|max:255'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();

		if (!Hash::check($request->password, $user->password)) {
			return $this->failed('Wrong password.');
		}

		$user->password = bcrypt($request->new_password);
		$user->save();

		return $this->message('Changed.');
	}
	// 修改安全密码
	public function storeSecurityCode(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'security_code' => 'required'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();
		if ($user->security_code != null) {
			return $this->failed('The security code is existed.');
		}

		$user->security_code = bcrypt($request->security_code);
		$user->save();
		return $this->created();
	}

	public function forgetSecurityCode()
	{
		$user = Auth::user();

		if ($this->hasSent($user->email)) {
			return $this->failed('Send mail too often.');
		}

		$code = rand_code();
		$this->setCode($user->email, $code);
		Mail::to($user->email)->queue(new ResetSecurityCodeMail($code));

		return $this->created();
	}

	public function resetSecurityCode(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'security_code' => 'required',
			'code' => 'required'
		]);

		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();

		if ($this->getCode($user->email) != $request->code) {
			return $this->failed('Wrong code.');
		}

		$this->forgetCode($user->email);
		$user->security_code = bcrypt($request->security_code);
		$user->save();

		return $this->message('Reseted.');
	}
}
