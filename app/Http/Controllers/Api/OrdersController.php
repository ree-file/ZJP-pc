<?php

namespace App\Http\Controllers\Api;

use App\Nest;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;

class OrdersController extends ApiController
{
	public function order(Request $request)
	{
		$order = Order::where('id', $request->order_id)->selling()
			->with('seller', 'buyer', 'nest.parent', 'nest.children', 'nest.children.children')->first();
		return $this->success(new OrderResource($order));
	}

    public function orders()
	{
		$orders = Order::selling()->with('seller')->paginate(10);
		return $this->success($orders);
	}

	public function store(Request $request)
	{
		$nest = Nest::find($request->nest_id);
		$this->authorize('update', $nest);

		$order_count = Order::selling()->where('nest_id', $nest->id)->count();

		if ($order_count > 0) {
			$this->failed('The order is on selling.');
		}
		$user = Auth::user();

		$order = new Order();
		$order->nest_id = $nest->id;
		$order->price = $request->price;
		$order->seller_id = $user->id;
		$order->save();

		return $this->created();
	}

	public function abandon(Request $request)
	{
		$order = Order::find($request->order_id);
		$this->authorize('update', $order);

		if ($order->status != 'selling') {
			return $this->notFound();
		}

		$order->status = 'abandoned';
		$order->save();

		return $this->message('Abandoned.');
	}

	public function buy(Request $request)
	{
		$order = Order::find($request->order_id);

		if ($order->status != 'selling') {
			return $this->notFound();
		}

		$user = Auth::user();

		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'order_id']), [
			'nest_id' => $order->nest_id,
			'seller_id' => $order->seller_id,
			'buyer_id' => $user->id,
			'price' => $order->price
		]);

		$this->beforePayment($payment, $user);

		try {
			DB::transaction(function () use ($payment){
				$nest = Nest::where('id', $payment->nest_id)->lockForUpdate()->first();
				$nest->user_id = $payment->buyer_id;
				$nest->save();

				$buyer = User::where('id', $payment->buyer_id)->lockForUpdate()->first();
				$buyer->money_active = $buyer->money_active - $payment->pay_active;
				$buyer->money_limit = $buyer->money_limit - $payment->pay_limit;
				$buyer->save();

				$seller = User::where('id', $payment->seller_id)->lockForUpdate()->first();
				$seller->money_active = $seller->money_active + $payment->pay_active;
				$seller->money_limit = $seller->money_limit + $payment->pay_limit;
				$seller->save();

				$order = Order::where('id', $payment->order_id)->lockForUpdate()->first();
				$order->buyer_id = $buyer->id;
				$order->status = 'finished';
				$order->save();
			}, 3);
		} catch (\Exception $e) {
			return $this->failed('Payment failed.');
		}
	}
}
