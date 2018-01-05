<?php

namespace App\Listeners;

use App\Events\ContractUpgraded;
use App\Nest;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class PayTribute
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
     * @param  ContractUpgraded  $event
     * @return void
     */
    public function handle(ContractUpgraded $event)
    {
		$contract = $event->contract;
		$eggs = $event->eggs;
		// 找到相对大3级的前代
		$nest = Nest::withDepth()->where('id', $contract->nest_id)->first();
		$ancestors = Nest::withDepth()->having('depth', '>=', $nest->depth + 3)->ancestorsOf($nest->id);

		DB::beginTransaction();
		try {
			$ancestor1 = $ancestors->where('depth', $nest->depth + 1);
			$ancestor2 = $ancestors->where('depth', $nest->depth + 2);
			$ancestor3 = $ancestors->where('depth', $nest->depth + 3);
			if ($ancestor1) {
				$user1 = User::where('id', $ancestor1->user_id)->lockForUpdate()->first();
				$income_active = $eggs * config('zjp.TRIBUTE_ANCESTOR_ONE_RATE') * config('zjp.EGG_VAL');
				$user1->money_active = $user1->money_active + $income_active;
				$user1->save();
			}
			if ($ancestor2) {
				$income_active = $eggs * config('zjp.TRIBUTE_ANCESTOR_TWO_RATE') * config('zjp.EGG_VAL');
				$user2 = User::where('id', $ancestor2->user_id)->lockForUpdate()->first();
				$user2->money_active = $user2->money_active + $income_active;
				$user2->save();
			}
			if ($ancestor3) {
				$income_active = $eggs * config('zjp.TRIBUTE_ANCESTOR_THREE_RATE') * config('zjp.EGG_VAL');
				$user3 = User::where('id', $ancestor3->user_id)->lockForUpdate()->first();
				$user3->money_active = $user3->money_active + $income_active;
				$user3->save();
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}
    }
}
