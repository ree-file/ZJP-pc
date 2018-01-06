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
		// 创建初始用户
		$user = new \App\User();
		$user->email = '1205730728@qq.com';
		$user->password = bcrypt('password');
		$user->security_code = bcrypt('123456');
		$user->money_active = 100000;
		$user->save();

		$nest = new \App\Nest();
		$nest->name = 'AAAA6666';
		$nest->user_id = 1;
		$nest->save();

		$contract = new \App\Contract();
		$contract->nest_id = $nest->id;
		$contract->is_finished = false;
		$contract->eggs = config('zjp.CONTRACT_LEVEL_ONE');
		$contract->save();

		$investRecord = new \App\InvestRecord();
		$investRecord->nest_id = $nest->id;
		$investRecord->user_id = $user->id;
		$investRecord->contract_id = $contract->id;
		$investRecord->type = 'store';
		$investRecord->eggs = $contract->eggs;
		$investRecord->save();
	}
}
