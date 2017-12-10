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
			if (! $token = JWTAuth::attempt($credentials)) {
				return $this->failed('Unauthroized', 401);
			}
		} catch (JWTException $e) {
			return $this->failed('Could not create token.', 500);
		}

		$cookie = cookie('jwt_token', $token, config('jwt.ttl'));

		return $this->created()->withCookie($cookie);
	}

	public function logout()
	{
		$cookie = Cookie::forget('jwt_token');

		return $this->message('Log out.')->withCookie($cookie);
	}
}