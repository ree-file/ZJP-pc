<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $fillable = [
		'status', 'user_id', 'parent_id', 'type', 'community'
	];

	public function seller()
	{
		return $this->belongsTo('App\User', 'seller_id');
	}

	public function buyer()
	{
		return $this->belongsTo('App\User', 'buyer_id');
	}

	public function nest()
	{
		return $this->belongsTo('App\Nest');
	}

	public function scopeSelling($query)
	{
		return $query->where('status', 'selling');
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
