<?php

namespace App\Listeners;

use App\Events\NestInvested;
use App\Events\NestUpgraded;
use App\IncomeRecord;
use App\Nest;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ShareBonus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NestUpgraded  $event
     * @return void
     */
    public function handle(NestInvested $event)
    {
    	// 获取事件目标
		$nest = $event->nest;
		$eggs = $event->eggs;

		// 找到相对大3级的前代
		$nest = Nest::withDepth()->where('id', $nest->id)->first();
		$ancestors = Nest::withDepth()->having('depth', '>=', $nest->depth - 3)->ancestorsOf($nest->id);


		DB::beginTransaction();
		try {
			$ancestor1 = $ancestors->where('depth', $nest->depth - 1)->first();
			$ancestor2 = $ancestors->where('depth', $nest->depth - 2)->first();
			$ancestor3 = $ancestors->where('depth', $nest->depth - 3)->first();

			// 如果前1级存在
			if ($ancestor1) {
				$income = $eggs * config('zjp.BONUS_ONE_RATE') * config('zjp.EGG_VAL');
				$user1 = User::where('id', $ancestor1->user_id)->lockForUpdate()->first();
				$user1->money_active = $user1->money_active + $income;
				$user1->save();

				$incomeRecord = new IncomeRecord();
				$incomeRecord->user_id = $user1->id;
				$incomeRecord->money = $income;
				$incomeRecord->type = 'bonus';
				$incomeRecord->nest_id = $ancestor1->id;
				$incomeRecord->save();
			}

			// 如果前2级存在
			if ($ancestor2) {
				$income = $eggs * config('zjp.BONUS_TWO_RATE') * config('zjp.EGG_VAL');
				$user2 = User::where('id', $ancestor2->user_id)->lockForUpdate()->first();
				$user2->money_active = $user2->money_active + $income;
				$user2->save();

				$incomeRecord = new IncomeRecord();
				$incomeRecord->user_id = $user2->id;
				$incomeRecord->money = $income;
				$incomeRecord->type = 'bonus';
				$incomeRecord->nest_id = $ancestor2->id;
				$incomeRecord->save();
			}

			// 如果前3级存在
			if ($ancestor3) {
				$income = $eggs * config('zjp.BONUS_THREE_RATE') * config('zjp.EGG_VAL');
				$user3 = User::where('id', $ancestor3->user_id)->lockForUpdate()->first();
				$user3->money_active = $user3->money_active + $income;
				$user3->save();

				$incomeRecord = new IncomeRecord();
				$incomeRecord->user_id = $user3->id;
				$incomeRecord->money = $income;
				$incomeRecord->type = 'bonus';
				$incomeRecord->nest_id = $ancestor3->id;
				$incomeRecord->save();
			}

			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}
    }
}
