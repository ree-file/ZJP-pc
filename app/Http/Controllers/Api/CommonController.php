<?php


namespace App\Http\Controllers\Api;


use App\Contract;
use App\Handlers\CodeCacheHandler;
use App\Http\Requests\ContractRequest;
use App\IncomeRecord;
use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CommonController extends ApiController
{
	public function index()
	{
		$webConfig = config('website');
		return $this->success($webConfig);
	}

	// 忘记密码
	public function forgetPassword(Request $request, CodeCacheHandler $cacher)
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

		if ($cacher->hasSent($user->email)) {
			return $this->failed('Send mail too often.');
		}

		$code = rand_code();
		$cacher->setCode($user->email, $code);

		Mail::to($user->email)->queue(new ResetPasswordMail($code));

		return $this->created();
	}

	// 重置密码
	public function resetPassword(Request $request, CodeCacheHandler $cacher)
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

		if ($cacher->getCode($user->email) != $request->code) {
			return $this->failed('Wrong code.');
		}

		$cacher->forgetCode($user->email);
		$user->password = bcrypt($request->password);
		$user->save();

		return $this->message('Reseted.');
	}
}