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
        $nest->name = 'AAA666';
		$nest->user_id = 1;
		$nest->community = 'A';
		$nest->save();

		$contract = new \App\Contract();
		$contract->nest_id = $nest->id;
		$contract->cycle_date = \Carbon\Carbon::today();
		$contract->eggs = 600;
		$contract->save();
    }
}
