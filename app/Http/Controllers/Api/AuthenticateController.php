<?php
namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends ApiController
{
	public function login(Request $request)
	{
		$credentials = $request->only('email', 'password');

		try {
			if (! $token = JWTAuth::attempt($credentials)) {
				return $this->failed('Unauthroized', 401);
			}

			if (User::where('email', $credentials['email'])->first()->is_freezed) {
				return $this->failed('Freezed.', 401);
			}
		} catch (JWTException $e) {
			return $this->failed('Could not create token.', 500);
		}

		//$cookie = cookie('jwt_token', $token, config('jwt.ttl'));

		return $this->success(['jwt_token' => $token]);
	}

	public function logout()
	{
		//$cookie = Cookie::forget('jwt_token');
		JWTAuth::setToken(JWTAuth::getToken())->invalidate();

		return $this->message('Log out.');
	}

	public function refresh(Request $request)
	{
		$newToken = $request->header('Authorization');
		$newToken = substr($newToken, 7);

		return $this->success(['jwt_token' => $newToken]);
	}
}