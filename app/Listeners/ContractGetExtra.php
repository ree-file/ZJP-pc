<?php

namespace App\Listeners;

use App\Contract;
use App\Events\ContractUpgraded;
use App\NestRecord;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractGetExtra
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
        $eggs = (int) $event->eggs;

		if ($contract->nest->inviter != null) {
			$inviter_id = $contract->nest->inviter->id;
			$cont = Contract::where('nest_id', $inviter_id)->latest()->first();
			if (!$cont->is_finished) {
				$cont->from_receivers = $cont->from_receivers + $eggs * (float) config('zjp.NEST_INVITE_PROFIT_RATE');
				if ($cont->from_receivers + $cont->from_community + $cont->from_weeks >= $cont->eggs * (float) config('zjp.CONTRACT_PROFITE_RATE')) {
					$cont->is_finished = true;
				}
				$cont->save();

				$nest_record = new NestRecord();
				$nest_record->nest_id = $cont->nest_id;
				$nest_record->contract_id = $cont->id;
				$nest_record->type = 'invite_got';
				$nest_record->eggs = $eggs * (float) config('zjp.NEST_INVITE_PROFIT_RATE');
				$nest_record->save();
			}
		}

		if ($contract->nest->parent != null) {
			$parent_id = $contract->nest->parent->id;
			$cont = Contract::where('nest_id', $parent_id)->latest()->first();
			if (!$cont->is_finished) {
				if ($contract->nest->community == 'B') {
					$cont->frostB = $cont->frostB + $eggs * (float) config('zjp.NEST_COMMUNITY_B_PROFIT_RATE');
					$cont->save();
				}
				if ($contract->nest->community == 'C') {
					$cont->frostC = $cont->frostC + $eggs * (float) config('zjp.NEST_COMMUNITY_C_PROFIT_RATE');
					$cont->save();
				}
			}

			if ($contract->nest->parent->parent != null) {
				$grandparent_id = $contract->nest->parent->parent->id;
				$cont = Contract::where('nest_id', $grandparent_id)->latest()->first();

				if (!$cont->is_finished) {
					if ($contract->nest->parent->community == 'B') {
						$cont->frostB = $cont->frostB + $eggs * (float) config('zjp.NEST_COMMUNITY_B_PROFIT_RATE');
						$cont->save();
					}
					if ($contract->nest->parent->community == 'C') {
						$cont->frostC = $cont->frostC + $eggs * (float) config('zjp.NEST_COMMUNITY_C_PROFIT_RATE');
						$cont->save();
					}
				}
			}
		}
    }
}
