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

	public function scopeLatest($query)
	{
		return $query->orderBy('id', 'desc')->take(1);
	}
}
