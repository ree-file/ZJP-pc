<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'security_code', 'money_active', 'money_limit', 'money_market', 'updated_at', 'is_freezed'
    ];

    public function cards()
	{
		return $this->hasMany('App\Card');
	}

	public function nests()
	{
		return $this->hasMany('App\Nest');
	}

	public function sold()
	{
		return $this->hasMany('App\Order', 'seller_id');
	}

	public function bought()
	{
		return $this->hasMany('App\Order', 'buyer_id');
	}

	public function supplies()
	{
		return $this->hasMany('App\Supply');
	}
}
