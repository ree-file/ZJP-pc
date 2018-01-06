<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Nest extends Model
{
	use NodeTrait;

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function contracts()
	{
		return $this->hasMany('App\Contract');
	}

	public function incomeRecords()
	{
		return $this->hasMany('App\IncomeRecord');
	}
}
