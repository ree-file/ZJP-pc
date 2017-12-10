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

	public function accelerators()
	{
		return $this->hasMany('App\Accelerator');
	}

	public function products()
	{
		return $this->hasMany('App\Accelerator', 'produtor_id');
	}
}
