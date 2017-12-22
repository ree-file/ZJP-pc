<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User();
        $user->email = '1205730728@ZJP.com';
        $user->password = bcrypt('password');
        $user->money_active = 100000;
		$user->money_market = 100000;
        $user->save();
    }
}
