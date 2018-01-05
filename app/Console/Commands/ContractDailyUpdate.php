<?php

namespace App\Console\Commands;

use App\Contract;
use App\IncomeRecord;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContractDailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:dailyUpdate';

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
			$contracts = Contract::where('is_finished', false)->lockForUpdate()->get();
			foreach ($contracts as $contract) {
				// 每日孵化蛋树
				$dailyEggs = $contract->eggs * config('zjp.CONTRACT_DAILY_HATCH_RATE');
				// 合约总获利蛋数
				$profitEggs = $contract->eggs * config('zjp.CONTRACT_PROFITE_RATE');

				// 如果孵化完成
				if ($contract->hatches + $dailyEggs >= $profitEggs) {
					// 本次获利蛋数
					$incomeEggs = $profitEggs - $contract->hatches;

					$contract->hatches = $profitEggs;
					$contract->is_finished = true;
					$contract->save();

					$increasedMoneyActive = $incomeEggs * config('zjp.EGG_VAL');

					$user = User::where('id', $contract->nest->user_id)->lockForUpdate()->first();
					$user->money_active = $user->money_active + $increasedMoneyActive;
					$user->save();

					$incomeRecord = new IncomeRecord();
					$incomeRecord->user_id = $user->id;
					$incomeRecord->money = $incomeRecord->money + $increasedMoneyActive;
					$incomeRecord->type = 'daily';
					$incomeRecord->nest_id = $contract->nest_id;
					$incomeRecord->save();
				} else {
					// 如果孵化未完成
					// 本次获利蛋数
					$incomeEggs = $dailyEggs;

					$contract->hatches = $contract->hatches + $incomeEggs;
					$contract->save();

					$increasedMoneyActive = $incomeEggs * config('zjp.EGG_VAL');

					$user = User::where('id', $contract->nest->user_id)->lockForUpdate()->first();
					$user->money_active = $user->money_active + $increasedMoneyActive;
					$user->save();

					$incomeRecord = new IncomeRecord();
					$incomeRecord->user_id = $user->id;
					$incomeRecord->money = $increasedMoneyActive;
					$incomeRecord->type = 'daily';
					$incomeRecord->nest_id = $contract->nest_id;
					$incomeRecord->save();
				}
			}
		}, 5);

		print '日常合约获利计算完成';
    }
}
