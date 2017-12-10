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
        $nest1 = new \App\Nest();
		$nest1->user_id = 1;
		$nest1->community = 'A';
		$nest1->save();

		$nest2 = new \App\Nest();
		$nest2->user_id = 1;
		$nest2->parent_id = 1;
		$nest2->community = 'A';
		$nest2->inviter_id = 1;
		$nest2->save();

		$nest3 = new \App\Nest();
		$nest3->user_id = 1;
		$nest3->parent_id = 1;
		$nest3->community = 'B';
		$nest3->inviter_id = 1;
		$nest3->save();

		$nest4 = new \App\Nest();
		$nest4->user_id = 1;
		$nest4->parent_id = 1;
		$nest4->community = 'C';
		$nest4->inviter_id = 1;
		$nest4->save();

		$nest5 = new \App\Nest();
		$nest5->user_id = 1;
		$nest5->parent_id = 3;
		$nest5->community = 'A';
		$nest5->inviter_id = 1;
		$nest5->save();

		$nest6 = new \App\Nest();
		$nest6->user_id = 1;
		$nest6->parent_id = 3;
		$nest6->community = 'B';
		$nest6->inviter_id = 1;
		$nest6->save();

		$nest7 = new \App\Nest();
		$nest7->user_id = 1;
		$nest7->parent_id = 3;
		$nest7->community = 'C';
		$nest7->inviter_id = 1;
		$nest7->save();

		$nest8 = new \App\Nest();
		$nest8->user_id = 1;
		$nest8->parent_id = 4;
		$nest8->community = 'A';
		$nest8->inviter_id = 4;
		$nest8->save();

		$nest9 = new \App\Nest();
		$nest9->user_id = 1;
		$nest9->parent_id = 4;
		$nest9->community = 'B';
		$nest9->inviter_id = 4;
		$nest9->save();

		$nest10 = new \App\Nest();
		$nest10->user_id = 1;
		$nest10->parent_id = 4;
		$nest10->community = 'C';
		$nest10->inviter_id = 4;
		$nest10->save();
    }
}
