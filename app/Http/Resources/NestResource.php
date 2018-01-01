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
		$childrenA_count = count($this->children->filter(function ($value, $key) {
			return $value->community == 'A';
		}));
		$childrenB_count = count($this->children->filter(function ($value, $key) {
			return $value->community == 'B';
		}));
		$childrenC_count = count($this->children->filter(function ($value, $key) {
			return $value->community == 'C';
		}));
		$grandchildrenA_count = count(collect($grandchildren)->filter(function ($value, $key) {
			return $value->community == 'A';
		}));
		$grandchildrenB_count = count(collect($grandchildren)->filter(function ($value, $key) {
			return $value->community == 'B';
		}));
		$grandchildrenC_count = count(collect($grandchildren)->filter(function ($value, $key) {
			return $value->community == 'C';
		}));
		$receivers_count = count($this->receivers);

		$receiversEggs = $this->receivers->pluck('contracts')->flatten()->sum('eggs');
		return [
			'id'   => $this->id,
			'name' => $this->name,
			'community' => $this->community,
			'created_at' => date($this->created_at),
			'inviter' => $this->inviter,
			'parent' => $this->parent,
			'contracts' => $this->contracts->sortByDesc('id')->flatten(),
			'analyse' => [
				'receivers_count' => $receivers_count,
				'childrenA_count' => $childrenA_count,
				'childrenB_count' => $childrenB_count,
				'childrenC_count' => $childrenC_count,
				'grandchildrenA_count' => $grandchildrenA_count,
				'grandchildrenB_count' => $grandchildrenB_count,
				'grandchildrenC_count' => $grandchildrenC_count,
				'receivers_eggs' => $receiversEggs
			]
		];
	}
}
