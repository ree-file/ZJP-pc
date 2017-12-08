<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nest extends Model
{
	protected $fillable = [
		'user_id', 'parent_id', 'type', 'community'
	];

	public function accelerators()
	{
		return $this->hasMany('App\Accelertor');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
