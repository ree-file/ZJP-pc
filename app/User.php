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
        'password', 'remember_token', 'security_code'
    ];

    public function cards()
	{
		return $this->hasMany('App\Card');
	}

	public function nests()
	{
		return $this->hasMany('App\Nest');
	}

	public function rechargeApplications()
	{
		return $this->hasMany('App\RechargeApplication');
	}

	public function withdrawalApplications()
	{
		return $this->hasMany('App\WithdrawalApplication');
	}

	public function transferRecordsOfPaying()
	{
		return $this->hasMany('App\TransferRecord', 'payer_id');
	}

	public function transferRecordsOfReceiving()
	{
		return $this->hasMany('App\TransferRecord', 'receiver_id');
	}

	public function incomeRecords()
	{
		return $this->hasMany('App\IncomeRecord');
	}

	public function investRecords()
	{
		return $this->hasMany('App\InvestRecord');
	}

	public function transactionRecordsOfSelling()
	{
		return $this->hasMany('App\TransactionRecord', 'seller_id');
	}

	public function transactionRecordsOfBuying()
	{
		return $this->hasMany('App\TransactionRecord', 'buyer_id');
	}

	/*
	 * 将废除
	 */
	public function orders()
	{
		return $this->hasMany('App\Order', 'seller_id');
	}

	public function bought()
	{
		return $this->hasMany('App\Order', 'buyer_id');
	}
}
