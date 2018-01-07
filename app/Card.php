<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	public $fillable = ['username', 'bankname', 'number'];

	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
