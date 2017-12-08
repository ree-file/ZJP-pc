<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends ApiController
{
	public function login()
	{
		$credentials = request()->only('email', 'password');

		try {
			if (! $token = JWTAuth::attempts($credentials)) {
				return $this->failed('Unauthroized', 401);
			}
		} catch (JWTException $e) {
			return $this->failed('Could not create token.', 500);
		}

		$cookie = new Cookie('jwt_token', $token, config('jwt.ttl'));

		return $this->created()->withCookie($cookie);
	}

	public function logout()
	{
		$cookie = Cookie::forget('jwt_cookie');

		return response()->withCookie($cookie);
	}
}