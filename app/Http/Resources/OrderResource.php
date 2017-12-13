<?php

namespace App\Http\Resources;

use App\Order;
use Illuminate\Http\Resources\Json\Resource;

class OrderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		$highest_price = Order::where('nest_id', $this->id)->finished()->max('price');
        return [
			'id'   => $this->id,
			'seller' => $this->seller,
			'status' => $this->status,
			'price' => $this->price,
			'nest' => $this->nest,
			'highest_price' => $highest_price
		];
    }
}
