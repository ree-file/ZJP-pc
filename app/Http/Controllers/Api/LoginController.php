<?php

namespace App\Http\Controllers\Api;

use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class LoginController extends ApiController
{
	public function changePassword(Request $request)
	{
		$this->validate($request, [
			'password' => 'required|max:255',
		]);

		$user = Auth::user();
		$user->password = bcrypt($request->password);
		$user->save();

		return $this->message('Password changed.');
	}

	public function forgetPassword(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
		]);

		$user = User::where('email', $request->email)->first();
		if (!$user) {
			return $this->notFound();
		}

		$code = str_random(4);
		$user->code = $code;
		$user->save();
		Mail::to($user->email)->queue(new ResetPasswordMail($code));

		return $this->created();
	}

	public function checkCode(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'code' => 'required',
		]);

		$user = User::where('email', $request->email)->where('code', $request->code)->first();
		if (!$user) {
			return $this->notFound();
		}

		return $this->message('Right code.');
	}

	public function resetPassword(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'code' => 'required',
			'password' => 'required|max:255'
		]);

		$user = User::where('email', $request->email)->where('code', $request->code)->first();
		if (!$user) {
			return $this->notFound();
		}

		$user->password = bcrypt($request->password);
		$user->code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		$user->save();
		return $this->message('Password reseted.');
	}
}
