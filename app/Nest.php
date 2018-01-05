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

	public function parent()
	{
		return $this->belongsTo('App\Nest', 'parent_id');
	}

	public function children()
	{
		return $this->hasMany('App\Nest', 'parent_id');
	}

	public function incomeRecords()
	{
		return $this->hasMany('App\IncomeRecord');
	}
}
