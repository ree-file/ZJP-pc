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
		$grandchildren = count($this->children) == 0 ? null : $this->children->pluck('children')->flatten();
		return [
			'id'   => $this->id,
			'name' => $this->name,
			'community' => $this->community,
			'created_at' => date($this->created_at),
			'inviter' => $this->inviter,
			'receivers' => $this->receivers,
			'parent' => $this->parent,
			'children' => $this->children,
			'contracts' => $this->contracts->sortByDesc('created_at'),
			'grandchildren' => $grandchildren
		];
	}
}
