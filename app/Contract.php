<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    public function nest()
	{
		return $this->belongsTo('App\Nest');
	}
}
