<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id'                => $this->id,
			'email'             => $this->email,
			'money_active'      => $this->money_active,
			'money_limit'       => $this->money_limit,
			'coins'				=> $this->coins,
			'is_freezed'        => $this->is_freezed,
			'created_at'		=> date($this->created_at),
			'has_security_code' => $this->security_code != null ? true : false
		];
    }
}
