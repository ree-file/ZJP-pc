<?php


namespace App\Http\Controllers\Api;


class CommonController extends ApiController
{
	public function common()
	{
		$zjp = config('zjp');
		return $this->success($zjp);
	}
}