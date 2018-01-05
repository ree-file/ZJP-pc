<?php

namespace App\Http\Resources;

use App\Contract;
use App\Nest;
use Illuminate\Http\Resources\Json\Resource;

class NestResource extends Resource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		// 找到相对深度为20的所有下级
		$descendants = Nest::withDepth()
			->having('depth', '<=', $this->depth + 20)
			->descendantsOf($this->id);

		// 下级数量
		$depth1Count = $descendants->where('depth', $this->depth + 1)->count();
		$depth2Count = $descendants->where('depth', $this->depth + 2)->count();
		$depth3Count = $descendants->where('depth', $this->depth + 3)->count();
		$descendantsCount = $descendants->count();

		return [
			'id'   => $this->id,
			'name' => $this->name,
			'created_at' => date($this->created_at),
			'parent' => $this->parent,
			'analyse' => [
				'depth1_count' => $depth1Count,
				'depth2_count' => $depth2Count,
				'depth3_count' => $depth3Count,
				'descendants_count' => $descendantsCount
			]
		];
	}
}
