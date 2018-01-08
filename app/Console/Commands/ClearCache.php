<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCache extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'site:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear site:cache cache information.';

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
		$this->call('config:clear');
		$this->call('route:clear');
		$this->call('view:clear');
		$this->call('clear-compiled');
	}
}
