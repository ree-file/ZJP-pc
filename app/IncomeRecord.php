<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncomeRecord extends Model
{
    public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function nest()
	{
		return $this->belongsTo('App\Nest');
	}
}
