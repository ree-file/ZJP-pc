<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Contract;
use App\Events\NestInvested;
use App\InvestRecord;
use App\Mail\ResetPasswordMail;
use App\Mail\UserCreatedMail;
use App\Nest;
use App\Order;
use App\Supply;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NestResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends ApiController
{
	// 为他人创建用户或为他人买巢
	public function store(Request $request)
	{
		// 进行字段验证
		$validator = Validator::make($request->all(), [
			'parent_name' => 'required',
			'eggs' => ['required', Rule::in([
				config('website.CONTRACT_LEVEL_ONE'),
				config('website.CONTRACT_LEVEL_TWO'),
				config('website.CONTRACT_LEVEL_THREE'),
				config('website.CONTRACT_LEVEL_FOUR'),
				config('website.CONTRACT_LEVEL_FIVE')])],
			'email' => 'required|email',
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 查询上家是否存在
		$parent = Nest::where('name', $request->parent_name)->first();
		if (! $parent) {
			return $this->failed('Parent not found.');
		}

		$password = null;

		DB::beginTransaction();
		try {
			// 锁付款用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();
			// 付款价格
			$price = $request->eggs * config('website.EGG_VAL');

			// 如果金额不足则终止
			if ($user->money_limit + $user->money_active + $user->money_withdrawal < $price) {
				throw new \Exception('Wallet no enough money.');
			}

			if ($user->money_limit >= $price) {
				// 如果限制金额充足
				$user->money_limit = $user->money_limit - $price;
			} else if ($user->money_limit + $user->money_active >= $price) {
				// 如果活动资金充足
				$user->money_active = $user->money_active - ($price - $user->money_limit);
				$user->money_limit = 0;
			} else {
				// 限制金额与活动资金都用光了
				$user->money_withdrawal = $user->money_withdrawal - ($price - $user->money_limit - $user->money_active);
				$user->money_active = 0;
				$user->money_limit = 0;
			}
			$user->save();

			// 查询目标用户是否存在
			$receiver = User::where('email', $request->email)->first();

			if (! $receiver) {
				// 若不存在，则创建新账户，同时存储密码
				$receiver = new User();
				$receiver->email = $request->email;
				// 生成随机密码，保存密码信息
				$password = rand_password();
				$receiver->password = bcrypt($password);
				$receiver->save();
			}

			$nest = new Nest();
			// 生成随机巢名
			$nest->name = rand_name();
			$nest->user_id = $receiver->id;
			// 为巢绑定上家并保存
			$nest->appendToNode($parent)->save();

			// 为巢生成一个新合同
			$contract = new Contract();
			$contract->eggs = $request->eggs;
			$contract->nest_id = $nest->id;
			$contract->save();

			// 创建一条投资记录
			$investRecord = new InvestRecord();
			$investRecord->eggs = $request->eggs;
			$investRecord->contract_id = $contract->id;
			$investRecord->nest_id = $nest->id;
			$investRecord->user_id = $user->id;
			$investRecord->type = 'store';
			$investRecord->money = $request->eggs * config('website.EGG_VAL');
			$investRecord->save();

			DB::commit();

			// 触发巢投资事件
			event(new NestInvested($nest, $request->eggs));
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		// 如果为新用户，发送携带密码的邮件
		if ($password != null) {
			Mail::to($receiver->email)->queue(new UserCreatedMail($password));
		}

		return $this->created();
	}
}
