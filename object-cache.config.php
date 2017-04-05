<?php

$config = [
	'pools' => [
		// Default/fallback controller.
		'\WordPress\Cache\Redis\CacheItemPool'         => [
			'config'        => [
				'servers' => [
					'ip'   => '127.0.0.1',
					'port' => '1112'
				],
			],
			'groups'        => [
				''
			],
			'prerequisites' => [
				'class' => 'Redis',
			]
		],
		// Use Memcached controller for transients.
		'\WordPress\Cache\Memcached\CacheItemPool'     => [
			'config'        => [
				'servers' => [
					'ip'   => '127.0.0.1',
					'port' => '1112'
				],
			],
			'groups'        => [
				'site-transient'
			],
			'prerequisites' => [
				'class' => 'Memcached',
			]
		],
		// Use Non Persistent Pool.
		'\WordPress\Cache\NonPersistent\CacheItemPool' => [
			'groups' => [
				'non-persistent'
			],
		],
	]
];
