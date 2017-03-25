<?php

namespace WordPress\Cache;

use Psr\Cache\CacheItemPoolInterface;

class PoolGroupConnector {
	/** @var array */
	protected $pool_groups = array();

	/** @var GroupManager Group Manager */
	protected $group_manager;

	public function __construct( GroupManager $group_manager ) {
		$this->group_manager = $group_manager;
	}

	/**
	 * Assigns a Pool to a group.
	 *
	 * @param CacheItemPoolInterface $pool  Pool to assign to a group.
	 * @param string                 $group Group to assign to.
	 */
	public function add( CacheItemPoolInterface $pool, $group ) {
		$group                       = $this->group_manager->get( $group );
		$this->pool_groups[ $group ] = $pool;
	}

	/**
	 * Gets the Pool responsible for the supplied group.
	 *
	 * @param string $group Group to get Pool for.
	 *
	 * @return CacheItemPoolInterface
	 */
	public function get_pool( $group ) {
		$group = $this->group_manager->get( $group );

		if ( isset( $this->pool_groups[ $group ] ) ) {
			return $this->pool_groups[ $group ];
		}

		return new Null\CacheItemPool();
	}
}