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
    	// mysql5.7以下数据库兼容问题
		Schema::defaultStringLength(191);
        if (env('LOAD_CONFIG') == true) {
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
