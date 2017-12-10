<?php

namespace App\Repositories;

use App\Accelerator;
use App\Contract;
use App\Nest;
use Carbon\Carbon;

class NestRepository
{
	protected $model;

	public function __construct(Nest $nest)
	{
		$this->model = $nest;
	}

	public function find($id)
	{
		return $this->model->find($id);
	}

	// 当前合约
	public function contract($nest_id)
	{
		$nest = $this->find($nest_id);
		return $nest->contracts->last();
	}

	// 历史合约总蛋数
	public function eggs_total($nest_id)
	{
		$nest = $this->find($nest_id);
		return $nest->contracts->sum('eggs');
	}

	public function community_B_get($nest_id)
	{
		$nest = $this->find($nest_id);
		$contract = $nest->contracts->last();
		$max = $contract->eggs * 0.3;
		$accelerators = Accelerator::where('contract', $contract->id)->where('effective_date', '<', Carbon::now())->get();
		$num = $accelerators->sum('eggs');
		return $max > $num ? $num : $max;
	}

	public function community_C_get($nest_id)
	{
		$nest = $this->find($nest_id);
		$contract = $nest->contracts->last();
		$contracts = $nest->contracts;
		$max = $contracts->sum('eggs') * 0.3;
		$accelerators = Accelerator::where('contract', $contract->id)->where('effective_date', '<', Carbon::now())->get();
		$num = $accelerators->sum('eggs');
		return $max > $num ? $num : $max;
	}
}