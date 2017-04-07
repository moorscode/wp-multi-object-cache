<?php

namespace MultiObjectCache\Cache;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

class PoolGroupConnector implements PoolGroupConnectorInterface {
	/** @var array */
	protected $pool_groups = array();

	/** @var GroupManager Group Manager */
	protected $group_manager;

	/**
	 * PoolGroupConnector constructor.
	 *
	 * @param GroupManager $group_manager
	 */
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
	 * @return AbstractCachePool
	 */
	public function get( $group ) {
		static $non_persistent_fallback_cache;
		$group = $this->group_manager->get( $group );

		if ( isset( $this->pool_groups[ $group ] ) ) {
			return $this->pool_groups[ $group ];
		}

		if ( isset( $this->pool_groups[ '' ] ) ) {
			return $this->pool_groups[ '' ];
		}

		if ( null === $non_persistent_fallback_cache ) {
			$non_persistent_fallback_cache = new ArrayCachePool();
		}

		return $non_persistent_fallback_cache;
	}
}
