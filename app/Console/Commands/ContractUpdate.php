<?php

namespace App\Console\Commands;

use App\Contract;
use App\Handlers\DBHandler;
use App\IncomeRecord;
use App\Nest;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContractUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日常合约获利计算';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		DB::transaction(function () {
			$dber = new DBHandler();

			$contracts = Contract::with('nest')
				->select('id', 'is_finished', 'updated_at', 'nest_id', 'hatches', 'eggs')
				->where('is_finished', false)
				->lockForUpdate()
				->get();

			$uids = $contracts->pluck('nest')->flatten()->pluck('user_id')->unique()->values();
			$users = User::whereIn('id', $uids)
				->select('id', 'money_active', 'money_limit', 'coins', 'updated_at')
				->lockForUpdate()->get();
			$incomeRecords = [];
			$now = now();

			foreach ($contracts as $contract) {
				// 每日孵化蛋树
				$dailyEggs = $contract->eggs * config('website.CONTRACT_DAILY_HATCH_RATE');
				// 合约总获利蛋数
				$profitEggs = $contract->eggs * config('website.CONTRACT_PROFITE_RATE');

				// 如果孵化完成
				if ($contract->hatches + $dailyEggs >= $profitEggs) {
					// 本次获利蛋数
					$incomeEggs = $profitEggs - $contract->hatches;

					$contract->hatches = $profitEggs;
					$contract->is_finished = true;
					$contract->updated_at = $now;

					$increasedMoneyActive = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_MONEY_ACTIVE_RATE');
					$increasedMoneyLimit = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_MONEY_LIMIT_RATE');
					$increasedCoins = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_COINS_RATE');

					$user = $users->where('id', $contract->nest->user_id)->first();
					$user->money_active = $user->money_active + $increasedMoneyActive;
					$user->money_limit = $user->money_limit + $increasedMoneyLimit;
					$user->coins = $user->coins + $increasedCoins;

					$incomeRecord = [];
					$incomeRecord['user_id'] = $user->id;
					$incomeRecord['money_active'] = $increasedMoneyActive;
					$incomeRecord['money_limit'] = $increasedMoneyLimit;
					$incomeRecord['coins'] = $increasedCoins;
					$incomeRecord['type'] = 'daily';
					$incomeRecord['nest_id'] = $contract->nest_id;
					$incomeRecord['created_at'] = $now;
					$incomeRecord['updated_at'] = $now;
					array_push($incomeRecords, $incomeRecord);
				} else {
					// 如果孵化未完成
					// 本次获利蛋数
					$incomeEggs = $dailyEggs;

					$contract->hatches = $contract->hatches + $incomeEggs;
					$contract->updated_at = $now;

					$increasedMoneyActive = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_MONEY_ACTIVE_RATE');
					$increasedMoneyLimit = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_MONEY_LIMIT_RATE');
					$increasedCoins = $incomeEggs * config('website.EGG_VAL') * config('website.CONTRACT_COINS_RATE');

					$user = $users->where('id', $contract->nest->user_id)->first();
					$user->money_active = $user->money_active + $increasedMoneyActive;
					$user->money_limit = $user->money_limit + $increasedMoneyLimit;
					$user->coins = $user->coins + $increasedCoins;

					$incomeRecord = [];
					$incomeRecord['user_id'] = $user->id;
					$incomeRecord['money_active'] = $increasedMoneyActive;
					$incomeRecord['money_limit'] = $increasedMoneyLimit;
					$incomeRecord['coins'] = $increasedCoins;
					$incomeRecord['type'] = 'daily';
					$incomeRecord['nest_id'] = $contract->nest_id;
					$incomeRecord['created_at'] = $now;
					$incomeRecord['updated_at'] = $now;
					array_push($incomeRecords, $incomeRecord);
				}
			}

			$contracts = $contracts->map(function ($item, $key) use ($now) {
				return collect($item)->only(['id', 'hatches', 'is_finished', 'updated_at']);
			});

			$dber->updateBatch('contracts', $contracts->toArray());
			$dber->updateBatch('users', $users->toArray());

			DB::table('income_records')->insert($incomeRecords);
		}, 5);

		print '日常合约获利计算完成';
    }
}
