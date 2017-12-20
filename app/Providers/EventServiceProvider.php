<?php

namespace App\Providers;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	use ApiResponse;
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ContractUpgraded' => [
            'App\Listeners\ContractGetExtra',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

		Event::listen('tymon.jwt.absent', function () {
			return $this->failed('No token provided.');
		});

		Event::listen('tymon.jwt.invalid', function () {
			return $this->failed('Token invalid.');
		});

		Event::listen('tymon.jwt.expired', function () {
			return $this->failed('Token expired.');
		});

		Event::listen('tymon.jwt.user_not_found', function () {
			return $this->failed('Token user not found.');
		});
    }
}
