<?php
return [
	// 用户配置
	'USER_MAXIMUM_CARDS' => '5', // 用户可登记的银行卡的最大数量
	// 猫窝及合约配置
	'MARKET_TRANSCATION_TAX_RATE' => '0.05', // 猫窝在市场交易完成时扣取的税率
	'EGG_VAL' => '5', // 蛋单位金额
	'CONTRACT_PROFITE_RATE' => '3', // 合约获利倍率
	'CONTRACT_DAILY_HATCH_RATE' => '0.01', // 每日孵化比例
	'CONTRACT_MONEY_ACTIVE_RATE' => '0.7', // 合约获取活动资金比率
	'CONTRACT_MONEY_LIMIT_RATE' => '0.2', // 合约获取限制资金比率
	'CONTRACT_COINS_RATE' => '0.1', // 合约获取猫币比例
	// 合约分级配置
	'CONTRACT_LEVEL_ONE' => '100', // 一级合约蛋数
	'CONTRACT_LEVEL_TWO' => '200', // 二级合约蛋数
	'CONTRACT_LEVEL_THREE' => '1000', // 三级合约蛋数
	'CONTRACT_LEVEL_FOUR' => '2000', // 四级合约蛋数
	'CONTRACT_LEVEL_FIVE' => '4000', // 五级合约蛋数
	// 猫窝分红配置
	'BONUS_ONE_RATE' => '0.13', // 上一级分红金额比率
	'BONUS_TWO_RATE' => '0.03', // 上二级分红金额比率
	'BONUS_THREE_RATE' => '0.02', // 上三级分红金额比率
	// 充值配置
	'RECHARGE_APPLICATION_MONEY_MIN' => '0', // 充值最低金额
	// 提现配置
	'MONEY_WITHDRAWAL_INCREASE_RATE' => '0.06', // 每日活动资金转为限制资金比例
	'WITHDRAWAL_APPLICATION_MONEY_MIN' => '0', // 提现最低金额
	'WITHDRAWAL_FEE_RATE' => '0.055', // 提现手续费比率
	// 转账配置
	'TRANSFER_MONEY_MIN' => '0', // 转账最低金额
	// 汇率
	'USD_TO_CNY' => '6.5'
];