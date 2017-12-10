<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accelerator extends Model
{
	protected $fillable = [
		'nest_id'
	];

	public function nest()
	{
		return $this->belongsTo('App\Nest');
	}

	public function contract()
	{
		return $this->belongsTo('App\Contract');
	}

	public function productor()
	{
		return $this->belongsTo('App\Contract', 'productor_id');
	}
}
