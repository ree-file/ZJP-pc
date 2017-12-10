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
        $user1 = new \App\User();
        $user1->email = 'user1@ZJP.com';
        $user1->password = bcrypt('password');
        $user1->save();
    }
}
