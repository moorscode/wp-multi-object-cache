<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Memcache\MemcacheCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;

class Memcache implements PoolBuilderInterface {

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = array() ) {
		$memcached = new \Memcache();

		return new MemcacheCachePool( $memcached );
	}
}
