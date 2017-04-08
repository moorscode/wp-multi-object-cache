<?php

namespace WPMultiObjectCache;

use Cache\Adapter\Common\AbstractCachePool;

interface PoolFactoryInterface {
	/**
	 * Gets a pool by type and configuration
	 *
	 * @param string $type   Type of Pool to retrieve.
	 * @param array  $config Optional. Configuration for creating the Pool.
	 *
	 * @return AbstractCachePool
	 * @throws \InvalidArgumentException
	 */
	public function get( $type, array $config = array() );
}
