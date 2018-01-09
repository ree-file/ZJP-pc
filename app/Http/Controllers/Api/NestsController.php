<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\Events\NestInvested;
use App\Handlers\DBHandler;
use App\Http\Resources\NestResource;
use App\IncomeRecord;
use App\InvestRecord;
use App\Nest;
use App\Order;
use App\TransactionRecord;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NestsController extends ApiController
{
	// 猫窝（在售列表）分页
	public function index(Request $request)
	{
		$nests = Nest::with(['user' => function ($query) {
			$query->select('id', 'email');
		}])->where('is_selling', true)
			->priceBetween($request->min, $request->max)
			->withOrder($request->ordeyBy)
			->simplePaginate(10);

		return $this->success($nests);
	}

	// 猫窝详情（包含下级统计信息）（若所有者查看将包含部分统计信息）
	public function show(Request $request, Nest $nest)
	{
		// 如果要求详细信息（包含统计，且为窝主请求）
		if ($request->tab == 'detail' && $nest->user_id == Auth::id()) {
			$nest = Nest::where('id', $nest->id)
				->with(['parent' => function ($query) {
					$query->select('id', 'name');
				}, 'user' => function ($query) {
					$query->select('id', 'email');
				}, 'contracts', 'incomeRecords', 'investRecords', 'transactionRecords'])
				->withDepth()
				->first();

			return $this->success(new NestResource($nest));
		}

		$nest = Nest::where('id', $nest->id)
			->with(['parent' => function ($query) {
				$query->select('id', 'name');
			}, 'user' => function ($query) {
				$query->select('id', 'email');
			}, 'transactionRecords'])
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
			$item->val = $item->eggs * config('website.EGG_VAL');
		});

		return $this->success($contracts);
	}

	// 猫窝投资记录（限定用户）
	public function investRecords(Nest $nest)
	{
		// 限定该用户曾经的投资记录
		$records = InvestRecord::where('nest_id', $nest->id)
			->where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		return $this->success($records);
	}

	// 猫窝收益记录（限定用户）
	public function incomeRecords(Nest $nest)
	{
		// 限定该用户曾经的收益记录
		$records = IncomeRecord::where('nest_id', $nest->id)
			->where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		return $this->success($records);
	}

	// 猫窝成交记录
	public function transactionRecords(Nest $nest)
	{
		$records = TransactionRecord::where('nest_id', $nest->id)
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
				config('website.CONTRACT_LEVEL_ONE'),
				config('website.CONTRACT_LEVEL_TWO'),
				config('website.CONTRACT_LEVEL_THREE'),
				config('website.CONTRACT_LEVEL_FOUR'),
				config('website.CONTRACT_LEVEL_FIVE')])]
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
				$user->money_withdrawal = $user->money_withdrawal - ($price - ($user->money_limit + $user->money_active));
				$user->money_active = 0;
				$user->money_limit = 0;
			}
			$user->save();

			$nest = new Nest();
			// 生成随机名猫窝
			$nest->name = rand_name();
			$nest->user_id = $user->id;
			// 为猫窝绑定上家并保存
			$nest->appendToNode($parent)->save();

			// 为巢生成一个新合同
			$contract = new Contract();
			$contract->eggs = $request->eggs;
			$contract->nest_id = $nest->id;
			$contract->save();

			// 创建一条投资记录
			$investRecord = new InvestRecord();
			$investRecord->contract_id = $contract->id;
			$investRecord->nest_id = $nest->id;
			$investRecord->user_id = $user->id;
			$investRecord->type = 'store';
			$investRecord->eggs = $request->eggs;
			$investRecord->money = $request->eggs * config('website.EGG_VAL');
			$investRecord->save();

			DB::commit();

			// 触发巢投资事件
			event(new NestInvested($nest, $request->eggs));
		} catch (\Exception $e) {

			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	// 出售猫窝
	public function sell(Request $request, Nest $nest)
	{
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
		if ($nest->is_selling) {
			return $this->failed('The nest is on selling.');
		}

		$nest->is_selling = true;
		$nest->price = $request->price;
		$nest->save();

		return $this->created();
	}

	// 购买猫窝
	public function buy(Nest $nest)
	{
		// 确认猫窝是否为在售状态
		if (! $nest->is_selling) {
			return $this->failed('Not on selling.');
		}

		// 防止用户购买自己订单
		if (Auth::id() == $nest->seller_id) {
			return $this->failed('Can not buy own nest.');
		}

		DB::beginTransaction();
		try {
			// 锁定付款用户
			$buyer = User::where('id', Auth::id())->lockForUpdate()->first();

			// 如果钱包金额不足
			if ($buyer->money_active + $buyer->money_withdrawal < $nest->price) {
				throw new \Exception('Wallet no enough money.');
			}

			// 如果用户活动资金充足
			if ($buyer->money_active >= $nest->price) {
				$buyer->money_active = $buyer->money_active - $nest->price;
			} else {
				// 用户活动资金不足
				$buyer->money_withdrawal = $buyer->money_withdrawal - ($nest->price - $buyer->money_active);
				$buyer->money_active = 0;
			}
			$buyer->save();

			// 扣税后收入的金额
			$income = $nest->price * (1 - config('website.MARKET_TRANSCATION_TAX_RATE'));

			// 锁定收款用户
			$seller = User::where('id', $nest->user_id)->lockForUpdate()->first();
			$seller->money_active = $seller->money_active + $income;
			$seller->save();

			// 将巢进行转移
			$nest = Nest::where('id', $nest->id)->lockForUpdate()->first();
			$nest->user_id = $buyer->id;
			$nest->is_selling = false;
			$nest->save();

			// 保存交易记录
			$transactionRecord = new TransactionRecord();
			$transactionRecord->seller_id = $seller->id;
			$transactionRecord->buyer_id = $buyer->id;
			$transactionRecord->price = $nest->price;
			$transactionRecord->nest_id = $nest->id;
			$transactionRecord->income = $income;
			$transactionRecord->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->message('Bought.');
	}

	// 取消出售猫窝
	public function unsell(Nest $nest)
	{
		// 检查猫窝是否为在售状态
		if (! $nest->is_selling) {
			return $this->failed('Not on selling.');
		}

		// 检查用户是否有操作权限
		$this->authorize('update', $nest);

		$nest->is_selling = false;
		$nest->save();

		return $this->message('Unsold.');
	}

	// 为猫窝创建一个新合约
	public function reinvest(Request $request, Nest $nest)
	{
		// 验证字段
		$validator = Validator::make($request->all(), [
			'eggs' => ['required', Rule::in([
				config('website.CONTRACT_LEVEL_ONE'),
				config('website.CONTRACT_LEVEL_TWO'),
				config('website.CONTRACT_LEVEL_THREE'),
				config('website.CONTRACT_LEVEL_FOUR'),
				config('website.CONTRACT_LEVEL_FIVE')])]
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
			$investRecord->money = $request->eggs * config('website.EGG_VAL');
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

	// 为猫窝的最新进行合约升级
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
			config('website.CONTRACT_LEVEL_ONE'),
			config('website.CONTRACT_LEVEL_TWO'),
			config('website.CONTRACT_LEVEL_THREE'),
			config('website.CONTRACT_LEVEL_FOUR'),
			config('website.CONTRACT_LEVEL_FIVE')])) {
			return $this->message('Eggs count wrong.');
		}

		DB::beginTransaction();
		try {
			// 锁定用户
			$user = User::where('id', Auth::id())->lockForUpdate()->first();
			// 付款金额
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
			$investRecord->money = $request->eggs * config('website.EGG_VAL');
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
