<?php

namespace App\Observers;

use App\Accelerator;
use App\Contract;
use Carbon\Carbon;

class ContractObserver
{
	public function created(Contract $contract)
	{
		if ($contract->nest->inviter != null) {
			$inviter_id = $contract->nest->inviter->id;
			$cont = Contract::where('nest_id', $inviter_id)->latest()->first();
			if (!$cont->is_finished) {
				$cont->from_receivers = $cont->from_receivers + $contract->eggs * config('zjp.contract.profit.invite');
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
					$cont->frostB = $cont->frostB + $contract->eggs * config('zjp.contract.profit.community-B');
					$cont->save();
				}
				if ($contract->nest->community == 'C') {
					$cont->frostC = $cont->frostC + $contract->eggs * config('zjp.contract.profit.community-C');
					$cont->save();
				}
			}

			if ($contract->nest->parent->parent != null) {
				$grandparent_id = $contract->nest->parent->parent->id;
				$cont = Contract::where('nest_id', $grandparent_id)->latest()->first();

				if (!$cont->is_finished) {
					if ($contract->nest->parent->community == 'B') {
						$cont->frostB = $cont->frostB + $contract->eggs * config('zjp.contract.profit.community-B');
						$cont->save();
					}
					if ($contract->nest->parent->community == 'C') {
						$cont->frostC = $cont->frostC + $contract->eggs * config('zjp.contract.profit.community-C');
						$cont->save();
					}
				}
			}
		}
	}
}