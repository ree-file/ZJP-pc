<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
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

	public function scopePriceBetween($query, $min, $max)
	{
		if ($min && $max) {
			return $query->where('price', '>=', $min)->where('price', '<=', $max);
		} else if ($min) {
			return $query->where('price', '>=', $min);
		} else if ($max) {
			return $query->where('price')->where('price', '<=', $max);
		}

		return $query;
	}

	public function scopeWithOrder($query, $order)
	{
		if ($order == 'desc') {
			return $query->orderBy('id', 'desc');
		}

		return $query;
	}
}
