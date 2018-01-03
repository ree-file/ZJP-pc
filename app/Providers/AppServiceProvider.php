<?php

namespace App\Providers;

use App\Contract;
use App\Observers\ContractObserver;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Schema::defaultStringLength(191);
        Contract::observe(ContractObserver::class);
        if (env('LOAD_CONFIG' == true)) {
			Config::load();
		}
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
