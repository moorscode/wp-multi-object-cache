<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Void\VoidCachePool;
use WPMultiObjectCache\PoolBuilderInterface;

class Null implements PoolBuilderInterface {

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = [] ) {
		return new VoidCachePool();
	}
}
