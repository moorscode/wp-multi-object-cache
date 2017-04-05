<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Memcached\MemcachedCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;

class Memcached implements PoolBuilderInterface {

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = array() ) {
		$memcached = new \Memcached();

		return new MemcachedCachePool( $memcached );
	}
}
