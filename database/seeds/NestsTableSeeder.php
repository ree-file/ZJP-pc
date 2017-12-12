<?php

use Illuminate\Database\Seeder;

class NestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nest = new \App\Nest();
        $nest->name = '1';
		$nest->user_id = 1;
		$nest->community = 'A';
		$nest->save();

		$contract = new \App\Contract();
		$contract->nest_id = $nest->id;
		$contract->eggs = 50;
		$contract->save();
    }
}
