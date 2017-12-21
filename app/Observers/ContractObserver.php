<?php

namespace App\Observers;

use App\Accelerator;
use App\Contract;
use App\NestRecord;
use Carbon\Carbon;

class ContractObserver
{
	public function created(Contract $contract)
	{
		if ($contract->nest->inviter != null) {
			$inviter_id = $contract->nest->inviter->id;
			$cont = Contract::where('nest_id', $inviter_id)->latest()->first();
			if (!$cont->is_finished) {
				$cont->from_receivers = $cont->from_receivers + $contract->eggs * (float) config('zjp.NEST_INVITE_PROFIT_RATE');
				if ($cont->from_receivers + $cont->from_community + $cont->from_weeks >= $cont->eggs * (float) config('zjp.CONTRACT_PROFITE_RATE')) {
					$cont->finished = true;
				}
				$cont->save();

				$nest_record = new NestRecord();
				$nest_record->nest_id = $cont->nest_id;
				$nest_record->contract_id = $cont->id;
				$nest_record->type = 'invite_got';
				$nest_record->eggs = $contract->eggs * (float) config('zjp.NEST_INVITE_PROFIT_RATE');
				$nest_record->save();
			}
		}

		if ($contract->nest->parent != null) {
			$parent_id = $contract->nest->parent->id;
			$cont = Contract::where('nest_id', $parent_id)->latest()->first();

			if (!$cont->is_finished) {
				if ($contract->nest->community == 'B') {
					$cont->frostB = $cont->frostB + $contract->eggs * (float) config('zjp.NEST_COMMUNITY_B_PROFIT_RATE');
					$cont->save();
				}
				if ($contract->nest->community == 'C') {
					$cont->frostC = $cont->frostC + $contract->eggs * (float) config('zjp.NEST_COMMUNITY_C_PROFIT_RATE');
					$cont->save();
				}
			}

			if ($contract->nest->parent->parent != null) {
				$grandparent_id = $contract->nest->parent->parent->id;
				$cont = Contract::where('nest_id', $grandparent_id)->latest()->first();

				if (!$cont->is_finished) {
					if ($contract->nest->parent->community == 'B') {
						$cont->frostB = $cont->frostB + $contract->eggs * (float) config('zjp.NEST_COMMUNITY_B_PROFIT_RATE');
						$cont->save();
					}

					if ($contract->nest->parent->community == 'C') {
						$cont->frostC = $cont->frostC + $contract->eggs * (float) config('zjp.NEST_COMMUNITY_C_PROFIT_RATE');
						$cont->save();
					}
				}
			}
		}
	}
}