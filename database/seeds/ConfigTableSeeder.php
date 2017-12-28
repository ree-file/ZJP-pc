<?php

use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_config')->insert([
        	[
        		'name' => 'zjp.USER_MAXIMUM_CARDS',
				'value' => '5',
				'description' => '用户可登记的银行卡的最大数量'
			],
        	[
        		'name' => 'zjp.MARKET_TRANSCATION_TAX_RATE',
				'value' => '0.05',
				'description' => '巢在市场交易完成时收取的税率'
			],
			[
				'name' => 'zjp.MONEY_MARKET_TO_ACTIVE_TAX_RATE',
				'value' => '0.05',
				'description' => '用户钱包中市场金额转为活动金额税率'
			],
			[
				'name' => 'zjp.EGG_VAL',
				'value' => '10',
				'description' => '一个蛋对应的美元额度'
			],
			[
				'name' => 'zjp.CONTRACT_PROFITE_RATE',
				'value' => '3',
				'description' => '合约获利倍率'
			],
			[
				'name' => 'zjp.CONTRACT_EXTRACT_LIMIT_RATE',
				'value' => '0.2',
				'description' => '合约提取时，只能提取到用户限制资金的蛋提取率'
			],
			[
				'name' => 'zjp.CONTRACT_LEVEL_ONE',
				'value' => '70',
				'description' => '一级合约蛋数'
			],
			[
				'name' => 'zjp.CONTRACT_LEVEL_TWO',
				'value' => '140',
				'description' => '二级合约蛋数'
			],
			[
				'name' => 'zjp.CONTRACT_LEVEL_THREE',
				'value' => '700',
				'description' => '三级合约蛋数'
			],
			[
				'name' => 'zjp.CONTRACT_LEVEL_FOUR',
				'value' => '1400',
				'description' => '四级合约蛋数'
			],
			[
				'name' => 'zjp.CONTRACT_LEVEL_FIVE',
				'value' => '2800',
				'description' => '五级合约蛋数'
			],
			[
				'name' => 'zjp.CONTRACT_CYCLE_DAYS',
				'value' => '6',
				'description' => '合约周期天数'
			],
			[
				'name' => 'zjp.CONTRACT_CYCLE_COMMUNITY_ADD_LIMIT_RATE',
				'value' => '0.3',
				'description' => '合约周期社区获取限制率'
			],
			[
				'name' => 'zjp.CONTRACT_CYCLE_PROFIT_RATE',
				'value' => '0.06',
				'description' => '合约周期获利率'
			],
			[
				'name' => 'zjp.CONTRACT_DAILY_EXTRACT_RATE',
				'value' => '0.06',
				'description' => '合约每日最多提取已孵化的比例'
			],
			[
				'name' => 'zjp.NEST_INVITE_PROFIT_RATE',
				'value' => '0.06',
				'description' => '巢给邀请者贡献的获利率'
			],
			[
				'name' => 'zjp.NEST_COMMUNITY_C_PROFIT_RATE',
				'value' => '0.06',
				'description' => '巢在社区C为上级贡献的获利率'
			],
			[
				'name' => 'zjp.USER_WITHDRAW_TAX_RATE',
				'value' => '0.055',
				'description' => '用户提现时收取的税率'
			]
		]);
    }
}
