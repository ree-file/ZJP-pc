<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$contracts = \App\Contract::where('is_finished', false)
		->lockForUpdate()
		->with('nest.contracts')->get();

	$nest_records = [];

	foreach ($contracts as $contract) {
		$maxB = $contract->eggs * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE');
		$from_B = $contract->frostB;
		if ($contract->frostB > $maxB) $from_B = $maxB;
		$from_B = floor($from_B);
		$contract->from_community = $contract->from_community + $from_B;

		$maxC = $contract->nest->contracts->sum('eggs') * (float)config('zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE');
		$from_C = $contract->frostC;
		if ($contract->frostC > $maxC) $from_C = $maxC;
		$from_C = floor($from_C);
		$contract->from_community = $contract->from_community + $from_C;
		$contract->frostB = 0;
		$contract->frostC = 0;

		$contract->from_weeks = floor($contract->from_week + $contract->eggs * (float)config('zjp.CONTRACT_CYCLE_PROFIT_RATE'));
		$contract->cycle_date = \Carbon\Carbon::today();
		if ($contract->from_community + $contract->from_weeks + $contract->from_inviter >= $contract->eggs * (float)config('zjp.CONTRACT_PROFITE_RATE')) {
			$contract->is_finished = true;
		}

		$nest_record = [
			'nest_id'     => $contract->nest_id,
			'contract_id' => $contract->id,
			'type'        => 'week_got',
			'eggs'        => floor($contract->eggs * (float)config('zjp.CONTRACT_CYCLE_PROFIT_RATE'))
		];

		array_push($nest_records, $nest_record);

		if ($from_B + $from_C > 0) {
			$nest_record = [
				'nest_id'     => $contract->nest_id,
				'contract_id' => $contract->id,
				'type'        => 'community_got',
				'eggs'        => $from_B + $from_C
			];
			array_push($nest_records, $nest_record);
		}
	}

	$contracts = $contracts->map(function ($item, $key) {
		return $item->only(['id', 'eggs', 'is_finished', 'cycle_date', 'frostB', 'frostC', 'from_weeks', 'from_community']);
	});

	dd($contracts);

});
