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
				'name' => 'website.USER_MAXIMUM_CARDS',
				'value' => '5',
				'description' => '用户可登记的银行卡的最大数量'
			],
			[
				'name' => 'website.USER_WITHDRAWAL_LIMIT',
				'value' => '1000',
				'description' => '用户提现上限'
			],
			[
				'name' => 'website.MARKET_TRANSCATION_TAX_RATE',
				'value' => '0.05',
				'description' => '猫窝在市场交易完成时扣取的税率'
			],
			[
				'name' => 'website.EGG_VAL',
				'value' => '1000',
				'description' => '蛋单位金额'
			],
			[
				'name' => 'website.CONTRACT_PROFITE_RATE',
				'value' => '3',
				'description' => '合约获利倍率'
			],
			[
				'name' => 'website.CONTRACT_DAILY_HATCH_RATE',
				'value' => '0.01',
				'description' => '每日孵化比例'
			],
			[
				'name' => 'website.CONTRACT_MONEY_ACTIVE_RATE',
				'value' => '0.7',
				'description' => '合约获取活动资金比率'
			],
			[
				'name' => 'website.CONTRACT_MONEY_LIMIT_RATE',
				'value' => '0.2',
				'description' => '合约获取限制资金比率'
			],
			[
				'name' => 'website.CONTRACT_COINS_RATE',
				'value' => '0.1',
				'description' => '合约获取猫币比例'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_ONE',
				'value' => '100',
				'description' => '一级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_TWO',
				'value' => '200',
				'description' => '二级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_THREE',
				'value' => '1000',
				'description' => '三级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_FOUR',
				'value' => '2000',
				'description' => '四级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_FOUR',
				'value' => '2000',
				'description' => '四级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_FIVE',
				'value' => '4000',
				'description' => '五级合约蛋数'
			],
			[
				'name' => 'website.CONTRACT_LEVEL_FIVE',
				'value' => '4000',
				'description' => '五级合约蛋数'
			],
			[
				'name' => 'website.BONUS_ONE_RATE',
				'value' => '0.13',
				'description' => '上一级分红金额比率'
			],
			[
				'name' => 'website.BONUS_TWO_RATE',
				'value' => '0.03',
				'description' => '上二级分红金额比率'
			],
			[
				'name' => 'website.BONUS_THREE_RATE',
				'value' => '0.02',
				'description' => '上三级分红金额比率'
			],
			[
				'name' => 'website.RECHARGE_APPLICATION_MONEY_MIN',
				'value' => '0',
				'description' => '充值最低金额'
			],
			[
				'name' => 'website.RECHARGE_APPLICATION_MONEY_MIN',
				'value' => '0',
				'description' => '充值最低金额'
			],
			[
				'name' => 'website.WITHDRAWAL_APPLICATION_MONEY_MIN',
				'value' => '0',
				'description' => '提现最低金额'
			],
			[
				'name' => 'website.WITHDRAWAL_FEE_RATE',
				'value' => '0.055',
				'description' => '提现手续费比率'
			],
			[
				'name' => 'website.TRANSFER_MONEY_MIN',
				'value' => '0',
				'description' => '转账最低金额'
			]
		]);
    }
}
