<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class TokenRefresh extends BaseMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next)
	{
		//$response = $next($request);

		try {
			$newToken = $this->auth->setRequest($request)->parseToken()->refresh();
		} catch (TokenExpiredException $e) {
			return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
		} catch (JWTException $e) {
			return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
		}

		// send the refreshed token back to the client
		$request->headers->set('Authorization', 'Bearer '.$newToken);

		return $next($request); // return $response
	}
}
