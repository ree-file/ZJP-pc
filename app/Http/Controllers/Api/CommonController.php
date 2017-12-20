<?php


namespace App\Http\Controllers\Api;


use App\Http\Requests\ContractRequest;
use App\Mail\ResetPasswordMail;
use App\Traits\CodeCacheHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CommonController extends ApiController
{
	use CodeCacheHelper;
	public function index()
	{
		$zjp = config('zjp');
		return $this->success($zjp);
	}
	// 忘记密码
	public function forgetPassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = User::where('email', $request->email)->first();
		if (!$user) {
			return $this->notFound();
		}

		if ($this->hasSent($user->email)) {
			return $this->failed('Send mail too often.');
		}

		$code = rand_code();
		$this->setCode($user->email, $code);
		Mail::to($user->email)->queue(new ResetPasswordMail($code));

		return $this->created();
	}

	public function resetPassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'code' => 'required',
			'password' => 'required|min:6|max:255'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = User::where('email', $request->email)->first();
		if (! $user) {
			return $this->notFound();
		}

		if ($this->getCode($user->email) != $request->code) {
			return $this->failed('Wrong code.');
		}

		$this->forgetCode($user->email);
		$user->password = bcrypt($request->password);
		$user->save();

		return $this->message('Reseted.');
	}
}