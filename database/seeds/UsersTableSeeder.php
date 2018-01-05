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
        $user->email = '1205730728@qq.com';
        $user->password = bcrypt('password');
        $user->security_code = bcrypt('123456');
        $user->money_active = 100000;
        $user->save();
    }
}
