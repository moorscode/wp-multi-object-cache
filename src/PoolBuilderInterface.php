<?php

namespace WPMultiObjectCache;

use Cache\Adapter\Common\AbstractCachePool;

interface PoolBuilderInterface {
	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = [] );
}
