<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheOptimize extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'site:cache';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cache routing, configure information, to speed up the website running speed.';

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
		$this->call('config:cache');
		$this->call('route:cache');
		$this->call('optimize', ['--force']);
	}
}
