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
use Illuminate\Validation\Rule;

class UsersController extends ApiController
{
	/*
	 * 查询用户拥有资源信息
	 * */

	public function user(Request $request)
	{
		$user = $request->user();
		return $this->success($user);
	}

	public function cards()
	{
		$user = Auth::user();
		$cards = Card::where('user_id', $user->id)->get();

		return $this->success($cards->toArray());
	}

	public function nests()
	{
		$user = Auth::user();
		$nests = Nest::where('user_id', $user->id)->get();

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

	/*
	 * 提交表单查询信息
	 */

	public function nest(Request $request)
	{
		$nest = Nest::where('id', $request->id)->with('inviter', 'receivers', 'parent', 'children.children')->first();

		if (! $nest) {
			return $this->notFound();
		}

		$this->authorize('update', $nest);
		return $this->success(new NestResource($nest));
	}

	/*
	 * 提交表单操作信息
	 * */

	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|unique:nests|max:100',
			'inviter_id' => 'required',
			'parent_id' => 'required',
			'community' => ['required', Rule::in(['A', 'B', 'C'])],
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in(config('zjp.contracts.type'))],
			'email' => 'required|email',
		]);

		$user = Auth::user();
		$payment = array_merge($request->only(['name', 'inviter_id', 'parent_id', 'community', 'pay_active', 'pay_limit', 'eggs', 'email']), [
			'price' => $request->eggs * config('zjp.contract.egg.val')
		]);
		$password = null;

		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			$getter = User::where('email', $payment['user_email'])->first();
			if (count($getter) == 0) {
				$getter = new User();
				$getter->email = $payment['email'];
				$password = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
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
			$nest->name = $payment['name'];
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
		if ($password != null) {
			Mail::to($getter->email)->queue(new UserCreatedMail($password));
		}

		return $this->created();
	}
}
