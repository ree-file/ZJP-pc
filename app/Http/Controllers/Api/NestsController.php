<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\Events\NestInvested;
use App\Http\Resources\NestResource;
use App\IncomeRecord;
use App\InvestRecord;
use App\Nest;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NestsController extends ApiController
{
	// 猫窝详情（包含下级统计信息）
	public function show(Nest $nest)
	{
		$nest = Nest::where('id', $nest->id)
			->with(['parent' => function ($query) {
				$query->select('name');
			}])
			->withDepth()
			->first();

		return $this->success(new NestResource($nest));
	}

	// 猫窝合约
	public function contracts(Nest $nest)
	{
		$contracts = Contract::where('nest_id', $nest->id)
			->orderBy('created_at', 'desc')
			->get();

		// 为每个合约添加计算出的价值属性
		$contracts = $contracts->each(function ($item, $key) {
			$item->val = $item->eggs * config('zjp.EGG_VAL');
		});

		return $this->success($contracts);
	}

	// 猫窝投资记录
	public function investRecords(Nest $nest)
	{
		// 限定该用户曾经的投资记录
		$records = InvestRecord::where('nest_id', $nest->id)
			->where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		return $this->success($records);
	}

	// 猫窝收益记录
	public function incomeRecords(Nest $nest)
	{
		// 限定该用户曾经的收益记录
		$records = IncomeRecord::where('nest_id', $nest->id)
			->where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		return $this->success($records);
	}

	// 为自己创建巢
	public function store(Request $request)
	{
		// 验证字段
		$validator = Validator::make($request->all(), [
			'parent_name' => 'required',
			'eggs'        => ['required', Rule::in([
				config('zjp.CONTRACT_LEVEL_ONE'),
				config('zjp.CONTRACT_LEVEL_TWO'),
				config('zjp.CONTRACT_LEVEL_THREE'),
				config('zjp.CONTRACT_LEVEL_FOUR'),
				config('zjp.CONTRACT_LEVEL_FIVE')])]
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 查询上家是否存在
		$parent = Nest::where('name', $request->parent_name)->first();
		if (!$parent) {
			return $this->failed('Parent not found.');
		}

		DB::beginTransaction();
		try {
			// 锁定付款用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();
			// 付款价格
			$price = $request->eggs * config('zjp.EGG_VAL');

			// 如果金额不足则终止
			if ($user->money_limit + $user->money_active < $price) {
				throw new \Exception('Wallet no enough money.');
			}

			if ($user->money_limit >= $price) {
				// 如果限制金额充足
				$user->money_limit = $user->money_limit - $price;
			} else {
				// 如果限制金额不充足
				$user->money_active = $user->money_active - ($price - $user->money_limit);
				$user->money_limit = 0;
			}
			$user->save();

			$nest = new Nest();
			// 生成随机巢名
			$nest->name = rand_name();
			$nest->user_id = $user->id;
			// 为巢绑定上家并保存
			$nest->appendToNode($parent)->save();

			// 为巢生成一个新合同
			$contract = new Contract();
			$contract->eggs = $request->eggs;
			$contract->nest_id = $nest->id;
			$contract->save();

			DB::commit();

			// 触发巢投资事件
			event(new NestInvested($nest, $request->eggs));
		} catch (\Exception $e) {

			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	// 出售巢
	public function sell(Request $request, Nest $nest)
	{
		// 验证字段
		$validator = Validator::make($request->all(), [
			'price' => 'required|numeric|min:0',
		]);

		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 验证用户是否有操作资格
		$this->authorize('update', $nest);

		// 查看巢是否已经在销售
		if (Order::where('status', 'selling')
			->where('nest_id', $nest->id)->first()) {
			return $this->failed('The order is on selling.');
		}

		$order = new Order();
		$order->nest_id = $nest->id;
		$order->price = $request->price;
		$order->seller_id = Auth::id();
		$order->save();

		return $this->created();
	}

	// 为巢创建一个新合约
	public function reinvest(Request $request, Nest $nest)
	{
		// 验证字段
		$validator = Validator::make($request->all(), [
			'eggs' => ['required', Rule::in([
				config('zjp.CONTRACT_LEVEL_ONE'),
				config('zjp.CONTRACT_LEVEL_TWO'),
				config('zjp.CONTRACT_LEVEL_THREE'),
				config('zjp.CONTRACT_LEVEL_FOUR'),
				config('zjp.CONTRACT_LEVEL_FIVE')])]
		]);
		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 验证用户是否有操作资格
		$this->authorize('update', $nest);

		// 检查该巢最新进行合约是否完成
		$contract = Contract::where('nest_id', $nest->id)->latest()->first();
		if (!$contract->is_finished) {
			return $this->failed('The lastest contract is not finished.');
		}

		DB::beginTransaction();
		try {
			// 锁定用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();
			// 付款金额
			$price = $request->eggs * config('zjp.EGG_VAL');

			// 如果金额不足则终止
			if ($user->money_limit + $user->money_active < $price) {
				throw new \Exception('Wallet no enough money.');
			}

			if ($user->money_limit >= $price) {
				// 如果限制金额充足
				$user->money_limit = $user->money_limit - $price;
			} else {
				// 如果限制金额不充足
				$user->money_active = $user->money_active - ($price - $user->money_limit);
				$user->money_limit = 0;
			}
			$user->save();

			$contract = new Contract();
			$contract->eggs = $request->eggs;
			$contract->nest_id = $nest->id;
			$contract->save();

			// 创建巢复投操作记录
			$investRecord = new InvestRecord();
			$investRecord->nest_id = $nest->id;
			$investRecord->contract_id = $contract->id;
			$investRecord->user_id = $user->id;
			$investRecord->type = 'reinvest';
			$investRecord->eggs = $request->eggs;
			$investRecord->save();

			DB::commit();
		} catch (\Exception $e) {

			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		// 触发巢投资事件
		event(new NestInvested($nest, $request->eggs));

		return $this->created();
	}

	// 为巢的最新进行合约升级
	public function upgrade(Request $request, Nest $nest)
	{
		// 验证字段
		$validator = Validator::make($request->all(), [
			'eggs'       => 'required|integer'
		]);
		// 验证失败
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		// 查看用户是否有资格操作
		$this->authorize('update', $nest);

		// 查看最新进行合约是否完成
		$contract = Contract::where('nest_id', $nest->id)->latest()->first();
		if ($contract->is_finished) {
			return $this->failed('The lastest contract is finished.');
		}

		// 查看请求蛋数与合约蛋数相加是否满足合约分级蛋数量
		if (!in_array(($request->eggs + $contract->eggs), [
			config('zjp.CONTRACT_LEVEL_ONE'),
			config('zjp.CONTRACT_LEVEL_TWO'),
			config('zjp.CONTRACT_LEVEL_THREE'),
			config('zjp.CONTRACT_LEVEL_FOUR'),
			config('zjp.CONTRACT_LEVEL_FIVE')])) {
			return $this->message('Eggs count wrong.');
		}

		DB::beginTransaction();
		try {
			// 锁定用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();
			// 付款金额
			$price = $request->eggs * config('zjp.EGG_VAL');

			// 如果金额不足则终止
			if ($user->money_limit + $user->money_active < $price) {
				throw new \Exception('Wallet no enough money.');
			}

			if ($user->money_limit >= $price) {
				// 如果限制金额充足
				$user->money_limit = $user->money_limit - $price;
			} else {
				// 如果限制金额不充足
				$user->money_active = $user->money_active - ($price - $user->money_limit);
				$user->money_limit = 0;
			}
			$user->save();

			// 锁定合约
			$contract = Contract::where('id', $contract->id)
				->lockForUpdate()
				->first();
			$contract->eggs = $contract->eggs + $request->eggs;
			$contract->save();

			// 创建巢升单操作记录
			$investRecord = new InvestRecord();
			$investRecord->nest_id = $nest->id;
			$investRecord->contract_id = $contract->id;
			$investRecord->user_id = $user->id;
			$investRecord->type = 'upgrade';
			$investRecord->eggs = $request->eggs;
			$investRecord->save();

			DB::commit();
		} catch (\Exception $e) {

			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		// 触发巢投资事件
		event(new NestInvested($nest, $request->eggs));

		return $this->created();
	}
}
