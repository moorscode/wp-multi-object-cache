<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilderInterface;

class PHP implements PoolBuilderInterface {
	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return CacheItemPoolInterface
	 */
	public function create( array $config = [] ) {
		return new ArrayCachePool();
	}
}
