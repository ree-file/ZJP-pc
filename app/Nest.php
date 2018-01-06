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

	public function investRecords()
	{
		return $this->hasMany('App\InvestRecord');
	}

	public function transactionRecords()
	{
		return $this->hasMany('App\TransactionRecord');
	}

	public function scopePriceBetween($query, $min, $max)
	{
		if ($min && $max) {
			return $query->where('price', '>=', $min)->where('price', '<=', $max);
		} else if ($min) {
			return $query->where('price', '>=', $min);
		} else if ($max) {
			return $query->where('price')->where('price', '<=', $max);
		}

		return $query;
	}

	public function scopeWithOrder($query, $order)
	{
		if ($order == 'desc') {
			return $query->orderBy('id', 'desc');
		}

		return $query;
	}
}
