<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
	public $fillable = ['nest_id', 'eggs'];

    public function nest()
	{
		return $this->belongsTo('App\Nest');
	}
}
