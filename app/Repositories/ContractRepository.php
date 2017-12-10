<?php

namespace App\Repositories;

use App\Contract;

class ContractRepository
{
	protected $model;

	public function __construct(Contract $contract)
	{
		$this->model = $contract;
	}

	public function find($contract_id)
	{
		return $this->model->find($contract_id);
	}

	public function eggs_hatched($contract_id)
	{

	}

	public function invite_got($contract_id)
	{
		$contract = $this->find($contract_id);
		$accelerators = $contract->accelerators->where('contract_id', $contract_id)->where('type', 'invite')->get();
		return $accelerators->sum('eggs');
	}

	public function comunity_1B_limit()
	{

	}

	public function comunity_1B_got($contract_id)
	{

	}
}