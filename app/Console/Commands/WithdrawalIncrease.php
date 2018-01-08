<?php

namespace App\Console\Commands;

use App\Handlers\DBHandler;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WithdrawalIncrease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawal:increase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户每日可提现金额从活动资金增加转换.';

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

			$users = User::where('money_active', '<>', 0)->lockForUpdate()->get();
			foreach($users as $user) {
				$transMoney = round($user->money_active * config('website.MONEY_WITHDRAWAL_INCREASE_RATE'), 2);
				$user->money_active = $user->money_active - $transMoney;
				$user->money_withdrawal = $user->money_withdrawal + $transMoney;
			}

			$dber->updateBatch('users', $users->toArray());
		}, 5);

		print '每日转换用户活动资金打入到可提现资金';
    }
}
