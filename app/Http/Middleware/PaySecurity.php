<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PaySecurity
{
	use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$user = Auth::user();
    	if ($request->security_code == null || !Hash::check($request->security_code, $user->security_code)) {
    		return $this->failed('Wrong security code.');
		}

        return $next($request);
    }
}
