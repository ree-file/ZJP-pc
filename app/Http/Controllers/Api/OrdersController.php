<?php

namespace App\Http\Controllers\Api;

use App\Nest;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;

class OrdersController extends ApiController
{
	public function index(Request $request)
	{
		$orders = Order::selling()->with('seller')->paginate(10);
		return $this->success($orders);
	}

	public function show(Order $order)
	{
		if (! $order) {
			return $this->notFound();
		}

		$order = Order::where('id', $order->id)
			->with('seller', 'buyer', 'nest', 'nest.children', 'nest.children.children')
			->first();

		return $this->success(new OrderResource($order));
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'nest_id' => 'required',
			'price' => 'required|numeric|min:0',
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

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

	public function abandon(Request $request, Order $order)
	{
		if (! $order) {
			return $this->notFound();
		}
		if ($order->status != 'selling') {
			return $this->failed('Not on selling.');
		}

		$this->authorize('update', $order);

		$order->status = 'abandoned';
		$order->save();

		return $this->message('Abandoned.');
	}

	public function buy(Request $request, Order $order)
	{
		if (! $order) {
			return $this->notFound();
		}
		if ($order->status != 'selling') {
			return $this->failed('Not in selling.');
		}

		$user = Auth::user();

		if ($user->id == $order->seller_id) {
			return $this->failed('Can not buy own order.');
		}

		$payment = [
			'nest_id' => $order->nest_id,
			'seller_id' => $order->seller_id,
			'price' => $order->price,
			'order_id' => $order->id
		];


		DB::beginTransaction();
		try {
			$buyer = User::where('id', $user->id)->lockForUpdate()->first();
			if ($buyer->money_market < $payment['price']) {
				throw new \Exception('Wallet no enough money.');
			}
			$buyer->money_market = $buyer->money_market - $payment['price'];
			$buyer->save();

			$seller = User::where('id', $payment['seller_id'])->lockForUpdate()->first();
			$seller->money_market = $seller->money_market + $payment['price'] * (1 - (float) config('zjp.MARKET_TRANSCATION_TAX_RATE'));
			$seller->save();

			$nest = Nest::where('id', $payment['nest_id'])->lockForUpdate()->first();
			$nest->user_id = $buyer->id;
			$nest->save();

			$order = Order::where('id', $payment['order_id'])->lockForUpdate()->first();
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
