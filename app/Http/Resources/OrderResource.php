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
		$receivers_count = count($this->nest->receivers);
		$children_count = count($this->nest->children);
		$grandchildren = count($this->nest->children) == 0 ? [] : $this->nest->children->pluck('children')->flatten();
		$grandchildren_count = count($grandchildren);
		$contracts_eggs = $this->nest->contracts->sum('eggs');
		$buyer = $this->buyer ? $this->buyer->only(['id', 'email']) : null;
        return [
			'id'   => $this->id,
			'seller' => $this->seller->only(['id', 'email']),
			'buyer' => $buyer,
			'status' => $this->status,
			'price' => $this->price,
			'nest' => collect($this->nest)->except(['contracts', 'children', 'receivers']),
			'analyse' => [
				'highest_price' => $highest_price,
				'receivers_count' => $receivers_count,
				'contracts_eggs' => $contracts_eggs,
				'children_count' => $children_count,
				'grandchildren_count' => $grandchildren_count
			]
		];
    }
}
