<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
	use ApiResponse;

	public function beforePayment($payment, $user)
	{
		if ($payment->pay_active + $payment->pay_limit < $payment->eggs * config('zjp.egg_val')) {
			return $this->failed('Not enough money.');
		}

		if ($payment->pay_limit > $user->money_limit || $payment->pay_active > $user->money_active) {
			return $this->failed('Wallet no enough money.');
		}
	}
}