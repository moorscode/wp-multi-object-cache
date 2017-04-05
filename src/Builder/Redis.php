<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Redis\RedisCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;

class Redis implements PoolBuilderInterface {
	public function create( array $config = array() ) {
		$redis = new \Redis();

		return new RedisCachePool( $redis );
	}
}
