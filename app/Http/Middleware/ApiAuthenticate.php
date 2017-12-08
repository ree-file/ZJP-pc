<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	try {
			$jwt_token = $request->cookie('jwt_token');
			$token = new Token($jwt_token);
			$payload = JWTAuth::decode($token);
			Auth::loginUsingId($payload['sub']);
		} catch(\Exceptiontion $e) {
    		$cookie = Cookie::forget('jwt_token');

			if ($e instanceof TokenExpiredException) {
				return response('Token expired.', 401)->withCookie($cookie);
			}
			return response('Invalid token.', 401)->withCookie($cookie);
		}

        return $next($request);
    }
}
