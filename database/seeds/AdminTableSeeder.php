<?php

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Menu;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Administrator::truncate();
		Administrator::create([
			'username' => 'admin',
			'password' => bcrypt('admin'),
			'name'     => 'Administrator',
		]);
		Administrator::create([
			'username' => 'admin2',
			'password' => bcrypt('admin2'),
			'name'     => 'admin'
		]);

		// create a role.
		Role::truncate();
		Role::create([
			'name' => '站长',
			'slug' => 'owner',
		]);

		Role::create([
			'name' => '管理员',
			'slug' => 'admin'
		]);

		// add role to user.
		Administrator::first()->roles()->save(Role::first());
		Administrator::find(2)->roles()->save(Role::find(2));

		//create a permission
		Permission::truncate();
		Permission::insert([
			[
				'name'        => '所有权限',
				'slug'        => '*',
				'http_method' => '',
				'http_path'   => '*',
			],
			[
				'name'        => '基本权限',
				'slug'        => 'auth.base',
				'http_method' => '',
				'http_path'   => "/auth/login\r\n/auth/logout\r\n/auth/setting/\r\n/\r\n/users\r\n/nests\r\n/cards\r\n/contracts\r\n/recharge_applications\r\n/withdrawal_applications/\r\n/analyse/\r\n/invest_records/\r\n/transfer_records\r\n/income_records\r\n/transaction_records",
			]
		]);

		Role::first()->permissions()->save(Permission::first());
		Role::find(2)->permissions()->save(Permission::find(2));

		// add default menus.
		Menu::truncate();
		Menu::insert([
			[
				'parent_id' => 0,
				'order' => 1,
				'title' => '主页',
				'icon' => 'fa-tachometer',
				'uri' => '/'
			],
			[
				'parent_id' => 0,
				'order' => 2,
				'title' => '用户',
				'icon' => 'fa-users',
				'uri' => '/users'
			],
			[
				'parent_id' => 0,
				'order' => 3,
				'title' => '猫窝',
				'icon' => 'fa-shopping-bag',
				'uri' => '/nests'
			],
			[
				'parent_id' => 0,
				'order' => 4,
				'title' => '充值申请',
				'icon' => 'fa-dollar',
				'uri' => '/recharge_applications'
			],
			[
				'parent_id' => 0,
				'order' => 5,
				'title' => '提现申请',
				'icon' => 'fa-money',
				'uri' => '/withdrawal_applications'
			],
			[
				'parent_id' => 0,
				'order' => 6,
				'title' => '银行卡',
				'icon' => 'fa-credit-card',
				'uri' => '/cards'
			],
			[
				'parent_id' => 0,
				'order' => 7,
				'title' => '合约',
				'icon' => 'fa-calendar-o',
				'uri' => '/contracts'
			],
			// 记录
			[
				'parent_id' => 0,
				'order' => 8,
				'title' => '记录',
				'icon' => 'fa-cubes',
				'uri' => ''
			],
			[
				'parent_id' => 8,
				'order' => 9,
				'title' => '投资记录',
				'icon' => 'fa-cube',
				'uri' => '/invest_records'
			],
			[
				'parent_id' => 8,
				'order' => 10,
				'title' => '收益记录',
				'icon' => 'fa-cube',
				'uri' => '/income_records'
			],
			[
				'parent_id' => 8,
				'order' => 11,
				'title' => '转账记录',
				'icon' => 'fa-cube',
				'uri' => '/transfer_records'
			],
			[
				'parent_id' => 8,
				'order' => 12,
				'title' => '交易记录',
				'icon' => 'fa-cube',
				'uri' => '/transaction_records'
			],
			[
				'parent_id' => 0,
				'order' => 13,
				'title' => '统计',
				'icon' => 'fa-bar-chart',
				'uri' => '/analyse'
			],
			[
				'parent_id' => 0,
				'order'     => 14,
				'title'     => '站点管理',
				'icon'      => 'fa-tasks',
				'uri'       => '',
			],
			[
				'parent_id' => 14,
				'order' => 15,
				'title' => '站点配置',
				'icon' => 'fa-toggle-on',
				'uri' => '/config'
			],
			[
				'parent_id' => 14,
				'order'     => 16,
				'title'     => '运行任务',
				'icon'      => 'fa-clock-o',
				'uri'       => '/scheduling',
			],
			[
				'parent_id' => 14,
				'order'     => 17,
				'title'     => '管理员',
				'icon'      => 'fa-users',
				'uri'       => 'auth/users',
			],
			[
				'parent_id' => 14,
				'order'     => 18,
				'title'     => '操作日志',
				'icon'      => 'fa-history',
				'uri'       => 'auth/logs',
			]
		]);

		// add role to menu
		Menu::find(14)->roles()->save(Role::first());
    }
}
