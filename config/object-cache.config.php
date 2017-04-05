<?php

$config = [
	'pools' => [
//		// Default/fallback controller.
//		'Redis'     => [
//			'config'        => [
//				'servers' => [
//					'ip'   => '127.0.0.1',
//					'port' => '1112'
//				],
//			],
//			'groups'        => [
//				''
//			],
//			'prerequisites' => [
//				'class' => 'Redis',
//			]
//		],
		// Use Memcached controller for transients.
		'Memcached' => [
			'config'        => [
				'servers' => [ [ '127.0.0.1', '1112' ] ],
			],
			'groups'        => [
				'site-transient'
			],
			'prerequisites' => [
				'class' => 'Memcached',
			]
		],
		// Use Non Persistent Pool.
		'PHP'       => [
			'groups' => [
				'non-persistent'
			],
		],
	]
];
