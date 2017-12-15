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
	/*
	 * 查所有在出售的市场单
	 * */

	public function orders()
	{
		$orders = Order::selling()->with('seller')->paginate(10);
		return $this->success($orders);
	}

	/*
	 * 提交表单，查市场单的具体信息
	 * */

	public function order(Request $request)
	{
		$order = Order::where('id', $request->order_id)->with('seller', 'buyer', 'nest.parent', 'nest.children', 'nest.children.children')->first();

		if (! $order) {
			return $this->notFound();
		}

		return $this->success(new OrderResource($order));
	}

	/*
	 * 操作表单，取消市场单
	 */

	public function abandon(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'price' => 'required|numeric|min:0',
		]);

		$order = Order::find($request->id);
		if (! $order || $order->status != 'selling') {
			return $this->notFound();
		}
		$this->authorize('update', $order);

		$order->status = 'abandoned';
		$order->save();

		return $this->message('Abandoned.');
	}

	/*
	 * 操作表单，创建、购买
	 */

	public function store(Request $request)
	{
		$this->validate($request, [
			'nest_id' => 'required',
			'price' => 'required|numeric|min:0',
		]);

		$nest = Nest::find($request->nest_id);
		$this->authorize('update', $nest);

		if (Order::selling()->where('nest_id', $nest->id)->count() > 0) {
			return $this->failed('The order is on selling.');
		}

		$user = Auth::user();

		$order = new Order();
		$order->nest_id = $nest->id;
		$order->price = $request->price;
		$order->seller_id = $user->id;
		$order->save();

		return $this->created();
	}

	public function buy(Request $request)
	{
		$this->validate($request, [
			'order_id' => 'required',
			'pay_market' => 'required|numeric|min:0',
		]);

		$order = Order::find($request->id);
		if (! $order || $order->status != 'selling') {
			return $this->notFound();
		}

		$user = Auth::user();

		$payment = array_merge($request->only(['pay_market', 'order_id']), [
			'nest_id' => $order->nest_id,
			'seller_id' => $order->seller_id,
			'price' => $order->price
		]);


		DB::beginTransaction();
		try {
			$nest = Nest::where('id', $payment->nest_id)->lockForUpdate()->first();
			$nest->user_id = $payment->buyer_id;
			$nest->save();

			$buyer = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_market'] < $payment['price']) {
				throw new \Exception('Not enough money.');
			}
			if ($payment['pay_market'] > $buyer->money_market) {
				throw new \Exception('Wallet no enough money.');
			}
			$buyer->money_market = $buyer->money_market - $payment['price'];
			$buyer->save();

			$seller = User::where('id', $payment->seller_id)->lockForUpdate()->first();
			$seller->money_market = $seller->money_market + $payment['price'] * (1 - config('zjp.user.tax.trade'));
			$seller->save();

			$order = Order::where('id', $payment->order_id)->lockForUpdate()->first();
			$order->buyer_id = $buyer->id;
			$order->status = 'finished';
			$order->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->message('Bought.');
	}
}
