<?php

use Illuminate\Database\Seeder;

class ContractsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Contract::create([
        	[
        		'nest_id' => 1,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 2,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 3,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 4,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 5,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 6,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 7,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 8,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 9,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 10,
				'user_id' => 1,
				'eggs' => 500
			],
			[
				'nest_id' => 1,
				'user_id' => 1,
				'eggs' => 500
			]
		]);
    }
}
