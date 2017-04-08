<?php

namespace WPMultiObjectCache;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

class PoolGroupConnector implements PoolGroupConnectorInterface {
	/** @var array */
	protected $poolGroups = array();

	/** @var GroupManager Group Manager */
	protected $groupManager;

	/**
	 * PoolGroupConnector constructor.
	 *
	 * @param GroupManager $groupManager
	 */
	public function __construct( GroupManager $groupManager ) {
		$this->groupManager = $groupManager;
	}

	/**
	 * Assigns a Pool to a group.
	 *
	 * @param CacheItemPoolInterface $pool  Pool to assign to a group.
	 * @param string                 $group Group to assign to.
	 */
	public function add( CacheItemPoolInterface $pool, $group ) {
		$group                      = $this->groupManager->get( $group );
		$this->poolGroups[ $group ] = $pool;
	}

	/**
	 * Gets the Pool responsible for the supplied group.
	 *
	 * @param string $group Group to get Pool for.
	 *
	 * @return AbstractCachePool
	 */
	public function get( $group ) {
		static $nonPersistentFallback;

		// See if the group has been registered directly.
		if ( isset( $this->poolGroups[ $group ] ) ) {
			return $this->poolGroups[ $group ];
		}

		// Lookup alias if not found.
		$group = $this->groupManager->get( $group );

		// Check if alias is present.
		if ( isset( $this->poolGroups[ $group ] ) ) {
			return $this->poolGroups[ $group ];
		}

		// Check if a default has been set.
		if ( isset( $this->poolGroups[''] ) ) {
			return $this->poolGroups[''];
		}

		// Fallback to statically set non-persistent cache.
		if ( null === $nonPersistentFallback ) {
			$nonPersistentFallback = new ArrayCachePool();
		}

		return $nonPersistentFallback;
	}
}
