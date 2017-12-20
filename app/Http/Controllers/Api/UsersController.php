<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Contract;
use App\Mail\ResetPasswordMail;
use App\Mail\UserCreatedMail;
use App\Nest;
use App\Order;
use App\Supply;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NestResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends ApiController
{
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'inviter_name' => 'required',
			'parent_name' => 'required',
			'community' => ['required', Rule::in(['A', 'B', 'C'])],
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in([
				(int) config('zjp.CONTRACT_LEVEL_ONE'),
				(int) config('zjp.CONTRACT_LEVEL_TWO'),
				(int) config('zjp.CONTRACT_LEVEL_THREE'),
				(int) config('zjp.CONTRACT_LEVEL_FOUR')])],
			'email' => 'required|email',
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$inviter = Nest::where('name', $request->inviter_name)->first();
		$parent = Nest::where('name', $request->parent_name)->first();

		if (! $inviter || ! $parent) {
			return $this->failed('The inviter or parent is not existed.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['community', 'pay_active', 'pay_limit', 'eggs', 'email']), [
			'price' => $request->eggs * (int) config('zjp.EGG_VAL'),
			'inviter_id' => $inviter->id,
			'parent_id' => $parent->id
		]);
		$password = null;

		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			$getter = User::where('email', $payment['email'])->first();
			if (! $getter) {
				$getter = new User();
				$getter->email = $payment['email'];
				$password = rand_password();
				$getter->password = bcrypt($password);
				$getter->save();
			}

			if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
				throw new \Exception('Not enough money.');
			}
			if ($payment['pay_active'] > $user->money_active || $payment['pay_limit'] > $user->money_limit) {
				throw new \Exception('Wallet no enough money.');
			}

			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_limit = $user->money_limit - $payment['pay_limit'];
			$user->save();

			$nest = new Nest();
			$nest->name = rand_name();
			$nest->inviter_id = $payment['inviter_id'];
			$nest->parent_id = $payment['parent_id'];
			$nest->community = $payment['community'];
			$nest->user_id = $getter->id;
			$nest->save();

			$contract = new Contract();
			$contract->eggs = $payment['eggs'];
			$contract->nest_id = $nest->id;
			$contract->cycle_date = Carbon::today();
			$contract->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		// 如果为新建用户，则发送邮件
		if ($password != null) {
			Mail::to($getter->email)->queue(new UserCreatedMail($password));
		}

		return $this->created();
	}
}
