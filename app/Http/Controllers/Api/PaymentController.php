<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Handlers\WithdrawalCacheHandler;
use App\RechargeApplication;
use App\TransferRecord;
use App\User;
use App\WithdrawalApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
	// 创建充值申请
	public function rechargeApplicationStore(Request $request, ImageUploadHandler $uploader)
	{
		// 充值最低金额
		$moneyMin = config('website.RECHARGE_APPLICATION_MONEY_MIN');

		// 验证字段是否符合规则
		$validator = Validator::make($request->all(), [
			'money'       => "required|numeric|min:$moneyMin",
			'image'       => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200',
			'card_number' => 'required|max:255'
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$rechargeApplication = new RechargeApplication();
		$rechargeApplication->user_id = Auth::id();
		$rechargeApplication->card_number = $request->card_number;
		$rechargeApplication->money = $request->money;

		// 如果存在照片上传，则存储照片
		if ($request->image) {
			$result = $uploader ->save($request->image, 'recharge_applications', Auth::id(), 800);
			if ($result) {
				$rechargeApplication->image = $result['path'];
			}
		}

		$rechargeApplication->save();

		return $this->created();
	}

	// 创建提现申请
	public function withdrawalApplicationStore(Request $request)
	{
		// 提现最低金额
		$moneyMin = config('website.WITHDRAWAL_APPLICATION_MONEY_MIN');

		// 验证字段是否符合规则
		$validator = Validator::make($request->all(), [
			'money'       => "required|numeric|min:$moneyMin",
			'card_number' => 'required|max:255'
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 预扣除用户活动资金
		DB::beginTransaction();
		try {
			$user = User::where('id', Auth::id())->lockForUpdate()->first();

			// 如果用户资金不足
			if ($user->money_withdrawal < $request->money) {
				throw new \Exception('Wallet money not enough.');
			}
			$user->money_withdrawal = $user->money_withdrawal - $request->money;
			$user->save();

			$withdrawalApplication = new WithdrawalApplication();
			$withdrawalApplication->user_id = Auth::id();
			$withdrawalApplication->money = $request->money;
			$withdrawalApplication->card_number = $request->card_number;
			$withdrawalApplication->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	// 发起转账
	public function transferRecordStore(Request $request)
	{
		// 转账最低金额
		$moneyMin = config('website.TRANSFER_MONEY_MIN');

		// 验证字段是否符合规则
		$validator = Validator::make($request->all(), [
			'money'   => "required|numeric|min:$moneyMin",
			'user_id' => 'required'
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 查询转账收款用户是否存在
		$receiver = User::where('id', $request->user_id)->first();
		if (! $receiver) {
			return $this->failed('Receiver not found.');
		}

		// 如果收款账户是本人
		if ($receiver->id == Auth::id()) {
			return $this->failed('Can not transfer to yourself.');
		}

		// 进行转账
		DB::beginTransaction();
		try {
			// 锁付款用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();

			// 如果付款用户资金不足
			if ($user->money_active + $user->money_withdrawal < $request->money) {
				throw new \Exception('Wallet money not enough.');
			}

			// 如果用户活动资金充足
			if ($user->money_active >= $request->money) {
				$user->money_active = $user->money_active - $request->money;
			} else {
				// 用户活动资金不足
				$user->money_withdrawal = $user->money_withdrawal - ($request->money - $user->money_active);
				$user->money_active = 0;
			}
			$user->save();

			// 收款用户金额及币增加数值
			$increasedMoneyActive = $request->money;

			// 锁收款用户
			$receiver = User::where('id', $receiver->id)->lockForUpdate()->first();

			$receiver->money_active = $receiver->money_active + $increasedMoneyActive;
			$receiver->save();

			$transferRecord = new TransferRecord();
			$transferRecord->payer_id = $user->id;
			$transferRecord->receiver_id = $receiver->id;
			$transferRecord->money = $request->money;
			$transferRecord->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}
}
