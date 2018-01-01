<?php

namespace App\Http\Resources;

use App\Contract;
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
		$grandchildren = count($this->children) == 0 ? [] : $this->children->pluck('children')->flatten();
		$children_count = count($this->children);
		$grandchildren_count = count($grandchildren);
		$receiversEggs = $this->receivers->pluck('contracts')->flatten()->sum('eggs');
		return [
			'id'   => $this->id,
			'name' => $this->name,
			'community' => $this->community,
			'created_at' => date($this->created_at),
			'inviter' => $this->inviter,
			'receivers' => $this->receivers->map(function ($item, $key) {
				return collect($item)->except(['contracts']);
			}),
			'parent' => $this->parent,
			'contracts' => $this->contracts->sortByDesc('id')->flatten(),
			'children_count' => $children_count,
			'grandchildren_count' => $grandchildren_count,
			'receivers_eggs' => $receiversEggs
		];
	}
}
