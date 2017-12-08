<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	protected $fillable = [
		'user_id', 'username', 'number', 'bankname'
	];

	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
