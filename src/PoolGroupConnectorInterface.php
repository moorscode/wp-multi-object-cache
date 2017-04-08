<?php

namespace WPMultiObjectCache;

use Cache\Adapter\Common\AbstractCachePool;
use Psr\Cache\CacheItemPoolInterface;

interface PoolGroupConnectorInterface {
	/**
	 * PoolGroupConnector constructor.
	 *
	 * @param GroupManagerInterface $groupManager
	 */
	public function __construct( GroupManagerInterface $groupManager );

	/**
	 * Assigns a Pool to a group.
	 *
	 * @param CacheItemPoolInterface $pool  Pool to assign to a group.
	 * @param string                 $group Group to assign to.
	 */
	public function add( CacheItemPoolInterface $pool, $group );

	/**
	 * Gets the Pool responsible for the supplied group.
	 *
	 * @param string $group Group to get Pool for.
	 *
	 * @return AbstractCachePool
	 */
	public function get( $group );
}
