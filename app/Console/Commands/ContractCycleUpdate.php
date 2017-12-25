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

			if (count($contracts) == 0) {
				print 'nothing to update';
				exit();
			}

			$nest_records = [];

			foreach ($contracts as $contract) {
				$maxB = $contract->eggs * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE') * (float) config('zjp.CONTRACT_PROFITE_RATE');
				$from_B = $contract->frostB;
				if ($contract->frostB > $maxB) $from_B = $maxB;
				$from_B = floor($from_B);
				$contract->from_community = $contract->from_community + $from_B;

				$maxC = $contract->nest->contracts->sum('eggs') * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE') * (float)config('zjp.CONTRACT_PROFITE_RATE');
				$from_C = $contract->frostC;
				if ($contract->frostC > $maxC) $from_C = $maxC;
				$from_C = floor($from_C);
				$contract->from_community = $contract->from_community + $from_C;
				$contract->frostB = 0;
				$contract->frostC = 0;

				$contract->from_weeks = $contract->from_weeks + floor($contract->eggs * (float)config('zjp.CONTRACT_CYCLE_PROFIT_RATE')* (float) config('zjp.CONTRACT_PROFITE_RATE'));
				$contract->cycle_date = \Carbon\Carbon::today();
				if ($contract->from_community + $contract->from_weeks + $contract->from_inviter >= $contract->eggs * (float)config('zjp.CONTRACT_PROFITE_RATE')) {
					$contract->is_finished = true;
				}
				$contract->updated_at = Carbon::now();

				$nest_record = [
					'nest_id'     => $contract->nest_id,
					'contract_id' => $contract->id,
					'type'        => 'week_got',
					'eggs'        => floor($contract->eggs * (float)config('zjp.CONTRACT_CYCLE_PROFIT_RATE')),
					'created_at'  => Carbon::now(),
					'updated_at'  => Carbon::now()
				];

				array_push($nest_records, $nest_record);

				if ($from_B + $from_C > 0) {
					$nest_record = [
						'nest_id'     => $contract->nest_id,
						'contract_id' => $contract->id,
						'type'        => 'community_got',
						'eggs'        => $from_B + $from_C,
						'created_at'  => Carbon::now(),
						'updated_at'  => Carbon::now()
					];
					array_push($nest_records, $nest_record);
				}
			}

			$contracts = $contracts->map(function ($item, $key) {
				return $item->only(['id', 'eggs', 'is_finished', 'cycle_date', 'frostB', 'frostC', 'from_weeks', 'from_community', 'updated_at']);
			});

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
