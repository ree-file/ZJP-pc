<?php

namespace App\Http\Controllers\Api;

use App\Nest;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;

class OrdersController extends ApiController
{
	// 所有在售的市场单
	public function index(Request $request)
	{
		// 可根据价格筛选及排序市场单，生成分页数据
		$orders = Order::with(['seller' => function ($query) {
			$query->select('id', 'email');
		}, 'buyer' => function ($query) {
			$query->select('id', 'email');
		}, 'nest' => function ($query) {
			$query->select('id', 'name');
		}])->where('status', 'selling')
			->priceBetween($request->min, $request->max)
			->withOrder($request->ordeyBy)
			->simplePaginate(10);

		return $this->success($orders);
	}

	// 取消在售市场单
	public function abandon(Order $order)
	{
		// 检查市场单是否为在售状态
		if ($order->status != 'selling') {
			return $this->failed('Not on selling.');
		}

		// 检查用户是否有操作权限
		$this->authorize('update', $order);

		$order->status = 'abandoned';
		$order->save();

		return $this->message('Abandoned.');
	}

	// 购买市场单
	public function buy(Order $order)
	{
		// 确认市场单是否为在售状态
		if ($order->status != 'selling') {
			return $this->failed('Not in selling.');
		}

		// 防止用户购买自己订单
		if (Auth::id() == $order->seller_id) {
			return $this->failed('Can not buy own order.');
		}

		DB::beginTransaction();
		try {
			// 锁定付款用户
			$buyer = User::where('id', Auth::id())->lockForUpdate()->first();

			// 如果钱包金额不足
			if ($buyer->money_active < $order->price) {
				throw new \Exception('Wallet no enough money.');
			}
			$buyer->money_active = $buyer->money_active - $order->price;
			$buyer->save();

			// 扣税后收入的金额
			$income = $order->price * (1 - config('website.MARKET_TRANSCATION_TAX_RATE'));

			// 锁定收款用户
			$seller = User::where('id', $order->seller_id)->lockForUpdate()->first();
			$seller->money_active = $seller->money_active + $income;
			$seller->save();

			// 将巢进行转移
			$nest = Nest::where('id', $order->nest_id)->lockForUpdate()->first();
			$nest->user_id = $buyer->id;
			$nest->save();

			// 更新市场单状态为完成，保存买家ID
			$order = Order::where('id', $order->id)->lockForUpdate()->first();
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
