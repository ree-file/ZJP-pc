<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Auth;

class UserFreezed
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
    	if ($user->is_freezed == true) {
    		return $this->failed('Freezed.');
		}

        return $next($request);
    }
}
