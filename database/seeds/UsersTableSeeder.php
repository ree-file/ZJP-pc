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
        $user->email = 'user@ZJP.com';
        $user->password = bcrypt('password');
        $user->money_active = 10000;
		$user->money_market = 10000;
        $user->save();
    }
}
