<?php
return [
	'user'     => [
		'card-max' => 5,
		'tax'      => [
			'market-to-active' => 0.05, // 市场资金提取到活动资金税率
			'trade'            => 0.05, // 市场交易成功后扣去税率
			'active-to-real'   => 0.2, // 用户提现时扣去税率
		],
	],
	'contract' => [
		'egg'    => [
			'val' => 1 // 蛋对应的美元额度
		],
		'type'   => [600, 1800, 6000, 18000],
		'cycle'  => [
			'date'            => 6, // 周期间隔
			'community-limit' => 0.3// 社区提取限率
		],
		'profit' => [
			'invite'      => 0.06, // 邀请增加利率
			'community-B' => 0.06, // B社区增加利率
			'community-C' => 0.06, // C社区增加利率
			'week'        => 0.06, // 每周增加利率
		]
	]
];