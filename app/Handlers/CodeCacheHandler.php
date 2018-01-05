<?php


namespace App\Handlers;


use Illuminate\Support\Facades\Cache;

class CodeCacheHandler
{
	// 是否存在最近发送缓存
	public function hasSent($email)
	{
		return Cache::has('recent_email_'.$email);
	}

	public function setCode($email, $code)
	{
		// 为邮箱创建验证码缓存
		Cache::put('code_'.$email, $code, 30);
		// 创建最近发送缓存
		Cache::put('recent_email_'.$email, '1', 1);
	}

	// 获得邮箱验证码
	public function getCode($email)
	{
		return Cache::get('code_'.$email);
	}

	// 忘记邮箱验证码缓存
	public function forgetCode($email)
	{
		Cache::forget('code_'.$email);
	}
}