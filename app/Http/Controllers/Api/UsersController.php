<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Contract;
use App\Nest;
use App\Order;
use App\Supply;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NestResource;
use Illuminate\Support\Facades\DB;

class UsersController extends ApiController
{
    public function user(Request $request)
	{
		$user = $request->user();
		return $this->success($user);
	}

	public function cards()
	{
		$user = Auth::user();
		$cards = Card::where('id', $user->id)->get();

		return $this->success($cards->toArray());
	}

	public function nests()
	{
		$user = Auth::user();
		$nests = Nest::where('user_id', $user->id)->get();

		return $this->success($nests);
	}

	public function nest(Request $request)
	{
		$nest = Nest::where('id', $request->id)->with('inviter', 'receivers', 'parent', 'children.children')->first();
		$this->authorize('update', $nest);
		return $this->success(new NestResource($nest));
	}

	public function orders()
	{
		$user = Auth::user();
		$orders = Order::where('seller_id', $user->id)->orWhere('buyer', $user->id)->with('nest')->get();
		return $this->success($orders);
	}

	public function supplies()
	{
		$user = Auth::user();
		$supplies = Supply::where('user_id', $user->id)->get();
		return $this->success($supplies);
	}

	public function store(Request $request)
	{
		$user = Auth::user();

		$new_user = User::where('email', $request->user_email)->first();
		if (! $new_user) {
			$new_user = new User();
			$new_user->email = $request->user_email;
			$new_user->password = bcrypt(str_random(6));
			$new_user->save();
		}


		$payment = array_merge($request->only(['name', 'inviter_id', 'parent_id', 'community', 'pay_active', 'pay_limit', 'eggs']), [
			'user_id' => $user->id,
			'price' => $request->eggs * config('zjp.egg.val'),
			'new_user' => $new_user->id
		]);

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment){
				$nest = new Nest();
				$nest->name = $payment->name;
				$nest->inviter_id = $payment->inviter_id;
				$nest->parent_id = $payment->parent_id;
				$nest->community = $payment->community;
				$nest->user_id = $payment->new_user;
				$nest->save();

				$user = User::where('id', $payment->user_id)->lockForUpdate()->first();
				$user->money_active = $user->money_active - $payment->pay_active;
				$user->money_limit = $user->money_limit - $payment->pay_limit;
				$user->save();

				$contract = new Contract();
				$contract->eggs = $payment->eggs;
				$contract->nest_id = $nest->id;
				$contract->cycle_date = Carbon::today();
				$contract->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Create failed.');
		}

		return $this->created();
	}

	public function changePassword(Request $request)
	{
		$user = Auth::user();
		$user->password = bcrypt($request->password);
		$user->save();
	}

	public function forgetPassword(Request $request)
	{
		$user = User::where('email', $request->email)->first();
		if (!$user) {
			$this->notFound();
		}

		$verify_code = str_random(6);
		$user->verify_code = $verify_code;
		$user->save();
	}

	public function checkVerifyCode(Request $request)
	{
		$user = User::where('email', $request->email)->where('verify_code', $request->verify_code)->first();
		if (!$user) {
			return $this->notFound();
		}
		return $this->message('Right code.');
	}

	public function resetPassword(Request $request)
	{
		$user = User::where('email', $request->email)->where('verify_code', $request->verify_code)->first();
		if (!$user) {
			return $this->notFound();
		}

		$user->password = bcrypt($request->password);
		$user->verify_code = str_random(10);
		$user->save();
		return $this->message('Password reseted.');
	}
}
