<?php

namespace WPMultiObjectCache;

use Psr\Cache\CacheItemPoolInterface;

interface PoolFactoryInterface {
	/**
	 * Gets a pool by type and configuration
	 *
	 * @param string $type   Type of Pool to retrieve.
	 * @param array  $config Optional. Configuration for creating the Pool.
	 *
	 * @return CacheItemPoolInterface
	 * @throws \InvalidArgumentException
	 */
	public function get( $type, array $config = array() );

	/**
	 * Provides a Void pool.
	 *
	 * @return CacheItemPoolInterface
	 */
	public function getVoidPool();
}
