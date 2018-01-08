<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccessExRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:accessExRate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '访问汇率，修改汇率数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$uri = 'https://api.fixer.io/latest?base=USD';

		$http = new Client();

		$response = $http->get($uri)->getBody();

		$USDTOCNY = json_decode($response, true)['rates']['CNY'];

		DB::table('admin_config')
			->where('name', 'website.USD_TO_CNY')
			->update([
				'value' => $USDTOCNY
			]);

		$this->call('config:clear');
		$this->call('config:cache');

		print '日常更新汇率成功';
    }
}
