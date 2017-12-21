<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nest extends Model
{
	protected $fillable = [
		'user_id', 'parent_id', 'type', 'community'
	];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function contracts()
	{
		return $this->hasMany('App\Contract');
	}

	public function receivers()
	{
		return $this->hasMany('App\Nest', 'inviter_id');
	}

	public function children()
	{
		return $this->hasMany('App\Nest', 'parent_id');
	}

	public function inviter()
	{
		return $this->belongsTo('App\Nest', 'inviter_id');
	}

	public function parent()
	{
		return $this->belongsTo('App\Nest', 'parent_id');
	}

	public function contract()
	{
		return $this->hasOne('App\Contract');
	}

	public function records()
	{
		return $this->hasMany('App\NestRecord');
	}
}
