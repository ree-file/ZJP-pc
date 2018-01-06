<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionRecord extends Model
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
}
