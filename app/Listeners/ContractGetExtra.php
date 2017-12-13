<?php

namespace App\Listeners;

use App\Contract;
use App\Events\ContractUpgraded;
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
        $eggs = $event->eggs;

		if ($contract->nest->inviter != null) {
			$inviter_id = $contract->nest->inviter->id;
			$cont = Contract::where('nest_id', $inviter_id)->latest()->first();
			if (!$cont->is_finished) {
				$cont->from_receivers = $cont->from_receivers + $eggs * config('zjp.rate.invite');
				if ($cont->from_receivers + $cont->from_community + $cont->from_weeks >= $cont->eggs) {
					$cont->finished = true;
				}
				$cont->save();
			}
		}

		if ($contract->nest->parent != null) {
			$parent_id = $contract->nest->parent->id;
			$cont = Contract::where('nest_id', $parent_id)->latest()->first();

			if (!$cont->is_finished) {
				if ($contract->nest->community == 'B') {
					$cont->frostB = $cont->frostB + $eggs * config('zjp.rate.communityB');
					$cont->save();
				}
				if ($contract->nest->community == 'C') {
					$cont->frostC = $cont->frostC + $eggs * config('zjp.rate.communityC');
					$cont->save();
				}
			}

			if ($contract->nest->parent->parent != null) {
				$grandparent_id = $contract->nest->parent->parent->id;
				$cont = Contract::where('nest_id', $grandparent_id)->latest()->first();

				if (!$cont->is_finished) {
					if ($contract->nest->parent->community == 'B') {
						$cont->frostB = $cont->frostB + $eggs * config('zjp.rate.communityB');
						$cont->save();
					}
					if ($contract->nest->parent->community == 'C') {
						$cont->frostC = $cont->frostC + $eggs * config('zjp.rate.communityC');
						$cont->save();
					}
				}
			}
		}
    }
}
