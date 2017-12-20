<?php
namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CodeCacheHelper
{
	public function hasSent($email)
	{
		return Cache::has('recent_email_'.$email);
	}

	public function setCode($email, $code)
	{
		Cache::put('code_'.$email, $code, 30);
		Cache::put('recent_email_'.$email, '1', 1);
	}

	public function getCode($email)
	{
		return Cache::get('code_'.$email);
	}

	public function forgetCode($email)
	{
		Cache::forget('code_'.$email);
	}
}