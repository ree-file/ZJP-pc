<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Handlers\CodeCacheHandler;
use App\Http\Resources\UserResource;
use App\IncomeRecord;
use App\Mail\ResetSecurityCodeMail;
use App\Nest;
use App\Order;
use App\RechargeApplication;
use App\TransferRecord;
use App\WithdrawalApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PrivateController extends ApiController
{
	// 个人收益记录
	public function incomeRecords(Request $request)
	{
		// 如果请求今日收益
		if ($request->tab == 'today') {
			// 分页
			$incomeRecords = IncomeRecord::where('user_id', Auth::id())
				->where('created_at', '>=', Carbon::today())
				->simplePaginate(10);

			return $this->success($incomeRecords);
		}

		// 分页
		$incomeRecords = IncomeRecord::where('user_id', Auth::id())
			->simplePaginate(10);

		return $this->success($incomeRecords);
	}

	// 个人收益记录统计信息
	public function incomeRecordsAnalyse()
	{
		// 今日收益
		$incomeRecordsToday = IncomeRecord::where('user_id', Auth::id())
			->where('created_at', '>=', Carbon::today())
			->get();

		// 所有收益
		$incomeRecords = IncomeRecord::where('user_id', Auth::id())
			->get();

		$analyseToday = [
			'money_active' => $incomeRecordsToday->sum('money_active'),
			'money_limit' => $incomeRecordsToday->sum('money_limit'),
			'coins' => $incomeRecordsToday->sum('coins'),
		];

		$analyse = [
			'money_active' => $incomeRecords->sum('money_active'),
			'money_limit' => $incomeRecords->sum('money_limit'),
			'coins' => $incomeRecords->sum('coins'),
		];

		$data = [
			'analyse_today' => $analyseToday,
			'analyse' => $analyse
		];

		return $this->success($data);
	}

	// 个人转账支付记录
	public function transferRecords(Request $request)
	{
		// 查询收款记录
		if ($request->tab == 'receiving') {
			$transferRecords = TransferRecord::where('receiver_id', Auth::id())
				->orderBy('created_at', 'desc')
				->get();

			return $this->success($transferRecords);
		}

		// 查询付款记录
		if ($request->tab == 'paying') {
			$transferRecords = TransferRecord::where('payer_id', Auth::id())
				->orderBy('created_at', 'desc')
				->get();

			return $this->success($transferRecords);
		}

		// 查询所有
		$transferRecords = TransferRecord::where('payer_id', Auth::id())
			->orwhere('receiver_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		return $this->success($transferRecords);
	}

	// 个人猫窝
	public function nests()
	{
		// 取出所有猫窝的同时取出相关联的合约蛋数和
		$nests = Nest::where('user_id', Auth::id())
			->withCount(['contracts as eggs_sum' => function ($query) {
				$query->select(DB::raw('SUM(eggs) as eggssum'));
			}, 'contracts as hatches_sum' => function ($query) {
				$query->select(DB::raw('SUM(hatches) as hatchessum'));
			}])->get();

		// 为每个猫窝添加计算出的价值属性
		$nests = $nests->each(function ($item, $key) {
			$item->val = $item->eggs_sum * config('website.EGG_VAL');
		});

		return $this->success($nests->toArray());
	}

	// 个人市场单
	public function orders()
	{
		$user = Auth::user();
		$orders = Order::where('seller_id', $user->id)->orWhere('buyer_id', $user->id)->with('nest')->get();
		return $this->success($orders);
	}

	// 个人信息
	public function user()
	{
		$user = Auth::user();
		return $this->success(new UserResource($user));
	}

	// 个人银行卡
	public function cards()
	{
		$user = Auth::user();
		$cards = Card::where('user_id', $user->id)->get();
		return $this->success($cards);
	}

	// 个人充值申请
	public function rechargeApplications()
	{
		$rechargeApplications = RechargeApplication::where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->simplePaginate(10);

		return $this->success($rechargeApplications);
	}

	// 个人提现申请
	public function withdrawalApplications()
	{
		$withdrawalApplications = WithdrawalApplication::where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->simplePaginate(10);

		return $this->success($withdrawalApplications);
	}

	// 修改密码
	public function changePassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => 'required|max:255',
			'new_password' => 'required|max:255'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();

		if (!Hash::check($request->password, $user->password)) {
			return $this->failed('Wrong password.');
		}

		$user->password = bcrypt($request->new_password);
		$user->save();

		return $this->message('Changed.');
	}

	// 创建安全密码
	public function storeSecurityCode(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'security_code' => 'required'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();
		if ($user->security_code != null) {
			return $this->failed('The security code is existed.');
		}

		$user->security_code = bcrypt($request->security_code);
		$user->save();
		return $this->created();
	}

	// 忘记安全密码
	public function forgetSecurityCode(CodeCacheHandler $cacher)
	{
		$user = Auth::user();

		if ($cacher->hasSent($user->email)) {
			return $this->failed('Send mail too often.');
		}

		$code = rand_code();
		$cacher->setCode($user->email, $code);
		Mail::to($user->email)->queue(new ResetSecurityCodeMail($code));

		return $this->created();
	}

	// 重置安全密码
	public function resetSecurityCode(Request $request, CodeCacheHandler $cacher)
	{
		$validator = Validator::make($request->all(), [
			'security_code' => 'required',
			'code' => 'required'
		]);

		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$user = Auth::user();

		if ($cacher->getCode($user->email) != $request->code) {
			return $this->failed('Wrong code.');
		}

		$cacher->forgetCode($user->email);
		$user->security_code = bcrypt($request->security_code);
		$user->save();

		return $this->message('Reseted.');
	}
}
