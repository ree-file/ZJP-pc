<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferRecord extends Model
{
    public function payer()
	{
		return $this->belongsTo('App\User', 'payer_id');
	}

	public function receiver()
	{
		return $this->belongsTo('App\User', 'receiver_id');
	}
}
