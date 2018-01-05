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
        $nest->name = 'AAA6666';
		$nest->user_id = 1;
		$nest->save(); // 存为根节点

		$contract = new \App\Contract();
		$contract->nest_id = $nest->id;
		$contract->is_finished = true;
		$contract->eggs = config('zjp.CONTRACT_LEVEL_ONE');
		$contract->hatches = config('zjp.CONTRACT_LEVEL_ONE') * 3;
		$contract->save();
    }
}
