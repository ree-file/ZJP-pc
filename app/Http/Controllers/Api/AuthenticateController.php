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


		$user = User::where('email', $credentials['email'])->first();

		if (! $user) {
			return $this->failed('Not found.', 403);
		}

		// 用户账号已被冻结
		if ($user->is_freezed) {
			return $this->failed('Freezed.', 403);
		}

		try {
			if (! $token = JWTAuth::attempt($credentials)) {
				return $this->failed('Unauthroized', 401);
			}


		} catch (JWTException $e) {
			return $this->failed('Could not create token.', 500);
		}

		$id = $user->id;

		// 返回 jwt_token 给客户端
		return $this->success(['jwt_token' => $token, 'id' => $id]);
	}

	public function logout()
	{
		// 将jwt_token 加入黑名单
		JWTAuth::setToken(JWTAuth::getToken())->invalidate();
		return $this->message('');
	}

	public function refresh(Request $request)
	{
		// 取出刷新好的 jwt_token ,，返回到 response 的 payload
		$newToken = $request->header('Authorization');
		$newToken = substr($newToken, 7);

		return $this->success(['jwt_token' => $newToken]);
	}
}