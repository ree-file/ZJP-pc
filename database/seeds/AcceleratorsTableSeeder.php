<?php

use Illuminate\Database\Seeder;

class AcceleratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Accelerator::create([
        	[
        		'contract_id' => 11,
				'nest_id' => 2,
				'type' => 'inivite',
				'creater_id' => 2,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 3,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 4,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 4,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 5,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 3,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],
			[
				'contract_id' => 11,
				'nest_id' => 3,
				'type' => 'inivite',
				'creater_id' => 3,
				'eggs' => 15
			],


		]);
    }
}
