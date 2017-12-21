<?php

namespace App\Console\Commands;

use App\Contract;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContractCycleUpdate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'contracts:cycle';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update contracts by `cycle_date`.';

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
			$contracts = Contract::where('is_finished', false)
				->where('cycle_date', '<=', Carbon::today()->subDays((int)config('zjp.CONTRACT_CYCLE_DAYS')))
				->lockForUpdate()
				->with('nest.contracts')->get();

			$nest_records = [];

			foreach ($contracts as $contract) {
				$maxB = $contract->eggs * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE');
				$from_B = $contract->frostB > $maxB ? $maxB : $contract->frostB;
				$contract->from_community = $contract->from_community + $from_B;

				$maxC = $contract->nest->contracts->sum('eggs') * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE');
				$from_C = $contract->frostC > $maxC ? $maxC : $contract->frostC;
				$contract->from_community = $contract->from_community + $from_C;

				$contract->from_weeks = $contract->from_week + $contract->eggs * (float)config('zjp.CONTRACT_CYCLE_PROFIT_RATE');

				$contract->cycle_date = Carbon::today();
				if ($contract->from_community + $contract->from_weeks + $contract->from_inviter >= $contract->eggs * (float)config('zjp.CONTRACT_PROFITE_RATE')) {
					$contract->is_finished = true;
				}

				$nest_record = [
					'nest_id'     => $contract->nest_id,
					'contract_id' => $contract->id,
					'type'        => 'week_got',
					'eggs'        => $contract->eggs * floor(config('zjp.CONTRACT_CYCLE_PROFIT_RATE'))
				];

				array_push($nest_records, $nest_record);

				if ($maxB + $maxC > 0) {
					$nest_record = [
						'nest_id'     => $contract->nest_id,
						'contract_id' => $contract->id,
						'type'        => 'community_got',
						'eggs'        => $maxB + $maxC
					];
					array_push($nest_records, $nest_record);
				}
			}

			$contracts = array_map('get_object_vars', $contracts->toArray());
			$this->updateBatch('contracts', $contracts);
			DB::table('nest_records')->insert($nest_records);
		}, 5);

		print 'Contracts cycle finished.';
	}

	public function updateBatch($tableName = "", $multipleData = [])
	{

		if ($tableName && !empty($multipleData)) {

			// column or fields to update
			$updateColumn = array_keys($multipleData[0]);
			$referenceColumn = $updateColumn[0]; //e.g id
			unset($updateColumn[0]);
			$whereIn = "";

			$q = "UPDATE " . $tableName . " SET ";
			foreach ($updateColumn as $uColumn) {
				$q .= $uColumn . " = CASE ";

				foreach ($multipleData as $data) {
					$q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" . $data[$uColumn] . "' ";
				}
				$q .= "ELSE " . $uColumn . " END, ";
			}
			foreach ($multipleData as $data) {
				$whereIn .= "'" . $data[$referenceColumn] . "', ";
			}
			$q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";

			// Update
			return DB::update(DB::raw($q));
		} else {
			return false;
		}

	}
}
