<?php

namespace WPMultiObjectCache;

use Psr\Cache\CacheItemPoolInterface;

interface PoolGroupConnectorInterface {
	/**
	 * PoolGroupConnector constructor.
	 *
	 * @param GroupManagerInterface $groupManager Group manager to use.
	 * @param PoolFactoryInterface  $factory      Factory to use.
	 */
	public function __construct( GroupManagerInterface $groupManager, PoolFactoryInterface $factory );

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
	 * @return CacheItemPoolInterface
	 */
	public function get( $group );
}
