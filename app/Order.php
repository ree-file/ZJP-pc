<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $fillable = [
		'status', 'user_id', 'parent_id', 'type', 'community'
	];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function accelerators()
	{
		return $this->hasMany('App\Accelerator');
	}

	public function scopeProcessing($query)
	{
		return $query->where('status', 'processing');
	}

	public function scopeFinished($query)
	{
		return $query->where('status', 'finished');
	}

	public function scopeAbandoned($query)
	{
		return $query->where('status', 'abandoned');
	}
}
