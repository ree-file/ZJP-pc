<?php

namespace App\Listeners;

use App\Events\NestUpgraded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ObtainTribute
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NestUpgraded  $event
     * @return void
     */

    // 处理前代获得分红
    public function handle(NestUpgraded $event)
    {
        //
    }
}
