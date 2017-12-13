<?php

namespace App\Http\Controllers\Api;

use App\Order;
use Illuminate\Http\Request;

class OrdersController extends ApiController
{
    public function orders()
	{
		$orders = Order::processing()->with('seller')->paginate(10);

		return $this->success($orders);
	}
}
