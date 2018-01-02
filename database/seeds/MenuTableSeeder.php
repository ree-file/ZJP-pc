<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_menu')->truncate();
		DB::table('admin_menu')->insert([
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
				'title' => '巢',
				'icon' => 'fa-bitbucket',
				'uri' => '/nests'
			],
			[
				'parent_id' => 0,
				'order' => 4,
				'title' => '申请',
				'icon' => 'fa-credit-card-alt',
				'uri' => '/supplies'
			],
			[
				'parent_id' => 0,
				'order' => 5,
				'title' => '市场',
				'icon' => 'fa-list-alt',
				'uri' => '/orders'
			],
			[
				'parent_id' => 0,
				'order' => 6,
				'title' => '统计',
				'icon' => 'fa-bar-chart',
				'uri' => '/analyse'
			],
			[
				'parent_id' => 0,
				'order' => 7,
				'title' => '配置',
				'icon' => 'fa-toggle-on',
				'uri' => '/config'
			],
			[
				'parent_id' => 0,
				'order' => 8,
				'title' => '运行任务',
				'icon' => 'fa-clock-o',
				'uri' => '/scheduling'
			]
		]);
    }
}
