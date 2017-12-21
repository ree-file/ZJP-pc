<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NestRecord extends Model
{
    public function nest()
	{
		return $this->belongsTo('App\Nest');
	}
}
