<?php

namespace App\Observers;

use App\Accelerator;
use App\Contract;
use Carbon\Carbon;

class ContractObserver
{
	public function created(Contract $contract)
	{
		if ($contract->nest->inviter->isNotEmpty()) {
			$inviter_id = $contract->nest->inviter->id;
			$cont = Contract::where('nest_id', $inviter_id)->orderBy('id', 'desc')->take(1)->first();
			if (!$cont->is_finished) {
				$cont->from_receivers = $cont->from_receivers + $contract->eggs * config('zjp.rate.invite');
				$cont->save();
			}
		}

		if ($contract->nest->parent->isNotEmpty()) {
			$parent_id = $contract->nest->parent->id;
			$cont = Contract::where('nest_id', $parent_id)->orderBy('id', 'desc')->take(1)->first();

			if (!$cont->is_finished) {
				if ($contract->nest->community == 'B') {
					$cont->frostB = $cont->frostB + $contract->eggs * config('zjp.rate.communityB');
					$cont->save();
				}
				if ($contract->nest->community == 'C') {
					$cont->frostC = $cont->frostC + $contract->eggs * config('zjp.rate.communityC');
					$cont->save();
				}
			}

			if ($contract->nest->parent->parent->isNotEmpty()) {
				$grandparent_id = $contract->nest->parent->parent->id;
				$cont = Contract::where('nest_id', $grandparent_id)->orderBy('id', 'desc')->take(1)->first();

				if (!$cont->is_finished) {
					if ($contract->nest->parent->community == 'B') {
						$cont->frostB = $cont->frostB + $contract->eggs * config('zjp.rate.communityB');
						$cont->save();
					}
					if ($contract->nest->parent->community == 'C') {
						$cont->frostC = $cont->frostC + $contract->eggs * config('zjp.rate.communityC');
						$cont->save();
					}
				}
			}
		}
	}
}