<?php

namespace App\Http\Controllers\Api;

use App\Contract;
use App\Events\ContractUpgraded;
use App\Http\Resources\NestResource;
use App\Nest;
use App\NestRecord;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NestsController extends ApiController
{
	public function index(Request $request)
	{
		if (! $request->has('name')) {
			return $this->failed('Need name field.');
		}
		$nests = Nest::where('name', $request->name)->with('children')->get();
		return $this->success($nests->toArray());
	}

	public function show(Nest $nest)
	{
		if (! $nest) {
			return $this->notFound();
		}

		$nest = Nest::where('id', $nest->id)->with('inviter', 'receivers', 'parent', 'children.children')->first();
		return $this->success(new NestResource($nest));
	}
	// 有支付操作
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'inviter_name' => 'required',
			'parent_name' => 'required',
			'community' => ['required', Rule::in(['A', 'B', 'C'])],
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in([
				(int) config('zjp.CONTRACT_LEVEL_ONE'),
				(int) config('zjp.CONTRACT_LEVEL_TWO'),
				(int) config('zjp.CONTRACT_LEVEL_THREE'),
				(int) config('zjp.CONTRACT_LEVEL_FOUR'),
				(int) config('zjp.CONTRACT_LEVEL_FIVE')])]
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}


		$inviter = Nest::where('name', $request->inviter_name)->first();
		$parent = Nest::where('name', $request->parent_name)->first();

		if (! $inviter||! $parent) {
			return $this->failed('The inviter or parent is not existed.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['community', 'pay_active', 'pay_limit', 'eggs']), [
			'price' => $request->eggs * config('zjp.EGG_VAL'),
			'inviter_id' => $inviter->id,
			'parent_id' => $parent->id
		]);


		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
				throw new \Exception('Not enough money.', 101);
			}
			if ($payment['pay_active'] > $user->money_active || $payment['pay_limit'] > $user->money_limit) {
				throw new \Exception('Wallet no enough money.', 101);
			}
			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_limit = $user->money_limit - $payment['pay_limit'];
			$user->save();

			$nest = new Nest();
			$nest->name = rand_name();
			$nest->inviter_id = $payment['inviter_id'];
			$nest->parent_id = $payment['parent_id'];
			$nest->community = $payment['community'];
			$nest->user_id = $user->id;
			$nest->save();

			$contract = new Contract();
			$contract->eggs = $payment['eggs'];
			$contract->nest_id = $nest->id;
			$contract->cycle_date = Carbon::today();
			$contract->save();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	public function reinvest(Request $request, Nest $nest) {
		$validator = Validator::make($request->all(), [
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => ['required', Rule::in([
				(int) config('zjp.CONTRACT_LEVEL_ONE'),
				(int) config('zjp.CONTRACT_LEVEL_TWO'),
				(int) config('zjp.CONTRACT_LEVEL_THREE'),
				(int) config('zjp.CONTRACT_LEVEL_FOUR'),
				(int) config('zjp.CONTRACT_LEVEL_FIVE')])]
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		if (! $nest) {
			return $this->notFound();
		}

		$this->authorize('update', $nest);

		$contract = Contract::where('nest_id', $nest->id)->latest()->first();
		if (!$contract->is_finished) {
			return $this->failed('The lastest contract is not finished.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs']), [
			'price' => $request->eggs * config('zjp.EGG_VAL'),
			'nest_id' => $nest->id
		]);

		DB::beginTransaction();
		try {
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
				throw new \Exception('No enough money.');
			}
			if ($payment['pay_active'] > $user->money_active || $payment['pay_limit'] > $user->money_limit) {
				throw new \Exception('Wallet no enough money.');
			}
			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_limit = $user->money_limit - $payment['pay_limit'];
			$user->save();

			$contract = new Contract();
			$contract->eggs = $payment['eggs'];
			$contract->cycle_date = Carbon::today();
			$contract->nest_id = $payment['nest_id'];
			$contract->save();

			$nest_record = new NestRecord();
			$nest_record->nest_id = $contract->nest_id;
			$nest_record->contract_id = $contract->id;
			$nest_record->user_id = $user->id;
			$nest_record->type = 'reinvest';
			$nest_record->eggs = $payment['eggs'];
			$nest_record->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		return $this->created();
	}

	public function upgrade(Request $request, Nest $nest) {
		$validator = Validator::make($request->all(), [
			'pay_active' => 'required|numeric|min:0',
			'pay_limit' => 'required|numeric|min:0',
			'eggs' => 'required|integer'
		]);
		if ($validator->fails()) {
			return $this->failed($validator->errors()->first());
		}

		$this->authorize('update', $nest);

		$contract = Contract::where('nest_id', $nest->id)->latest()->first();
		if ($contract->is_finished) {
			return $this->failed('The lastest contract is finished.');
		}

		if (!in_array((int) ($request->eggs + $contract->eggs), [
			(int) config('zjp.CONTRACT_LEVEL_ONE'),
			(int) config('zjp.CONTRACT_LEVEL_TWO'),
			(int) config('zjp.CONTRACT_LEVEL_THREE'),
			(int) config('zjp.CONTRACT_LEVEL_FOUR'),
			(int) config('zjp.CONTRACT_LEVEL_FIVE')])) {
			return $this->message('Eggs count wrong.');
		}

		$user = Auth::user();
		$payment = array_merge($request->only(['pay_active', 'pay_limit', 'eggs']), [
			'price' => $request->eggs * config('zjp.EGG_VAL'),
			'contract_id' => $contract->id
		]);

		DB::beginTransaction();
		try {
			$contract = Contract::where('id', $payment['contract_id'])->lockForUpdate()->with('nest.parent.parent')->first();
			if ($contract->is_finished) {
				throw new \Exception('The contract is finished.');
			}
			$user = User::where('id', $user->id)->lockForUpdate()->first();
			if ($payment['pay_active'] + $payment['pay_limit'] < $payment['price']) {
				throw new \Exception('No enough money.');
			}
			if ($payment['pay_active'] > $user->money_active || $payment['pay_limit'] > $user->money_limit) {
				throw new \Exception('Wallet no enough money.');
			}
			$user->money_active = $user->money_active - $payment['pay_active'];
			$user->money_limit = $user->money_limit - $payment['pay_limit'];
			$user->save();

			$contract->eggs = $contract->eggs + $payment['eggs'];
			$contract->save();

			$nest_record = new NestRecord();
			$nest_record->nest_id = $contract->nest_id;
			$nest_record->contract_id = $contract->id;
			$nest_record->user_id = $user->id;
			$nest_record->type = 'upgrade';
			$nest_record->eggs = $payment['eggs'];
			$nest_record->save();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->failed($e->getMessage());
		}

		event(new ContractUpgraded($contract, $payment['eggs']));

		return $this->created();
	}

	public function records(Request $request, Nest $nest) {
		if (! $nest) {
			return $this->notFound();
		}
		$user = Auth::user();

		$records = $nest->records;
		$got_records = $records->filter(function ($value, $key) {
			return in_array($value->type, ['week_got', 'invite_got', 'community_got']);
		})->toArray();
		$extract_records = $records->filter(function ($value, $key) use ($user) {
			return $value->type == 'extract' && $value->user_id == $user->id;
		})->toArray();
		$contract_records = $records->filter(function ($value, $key) use ($user) {
			return in_array($value->type, ['reinvest', 'upgrade']) && $value->user_id == $user->id;
		})->toArray();

		$data = [
			'got_records' => $got_records,
			'extract_records' => $extract_records,
			'contract_records' => $contract_records,
		];

		return $this->success($data);
	}
}
