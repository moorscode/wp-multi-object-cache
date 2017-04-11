<?php

namespace WPMultiObjectCache;

use Psr\Cache\CacheItemPoolInterface;

class PoolGroupConnector implements PoolGroupConnectorInterface {
	/** @var CacheItemPoolInterface[] */
	protected $poolGroups = [];

	/** @var GroupManagerInterface Group Manager */
	protected $groupManager;

	/** @var PoolFactoryInterface Factory */
	protected $factory;

	/**
	 * PoolGroupConnector constructor.
	 *
	 * @param GroupManagerInterface $groupManager Group manager to use.
	 * @param PoolFactoryInterface  $factory      Factory to use.
	 */
	public function __construct( GroupManagerInterface $groupManager, PoolFactoryInterface $factory ) {
		$this->groupManager = $groupManager;
		$this->factory      = $factory;
	}

	/**
	 * Assigns a Pool to a group.
	 *
	 * @param CacheItemPoolInterface $pool  Pool to assign to a group.
	 * @param string                 $group Group to assign to.
	 */
	public function add( CacheItemPoolInterface $pool, $group ) {
		$this->poolGroups[ $this->groupManager->get( $group ) ] = $pool;
	}

	/**
	 * Gets the Pool responsible for the supplied group.
	 *
	 * @param string $group Group to get Pool for.
	 *
	 * @return CacheItemPoolInterface
	 */
	public function get( $group ) {
		// See if the group has been registered directly.
		$pool = $this->getGroup( $group );

		// Lookup alias if not found.
		if ( null === $pool ) {
			// Check if alias is present.
			$pool = $this->getGroup( $this->groupManager->get( $group ) );
		}

		if ( null === $pool ) {
			$pool = $this->getGroup( '' );
		}

		if ( null === $pool ) {
			$pool = $this->factory->getFallbackPool();
		}

		return $pool;
	}

	/**
	 * Get the group if it exists
	 *
	 * @param string $group Group to fetch.
	 *
	 * @return CacheItemPoolInterface|null
	 */
	protected function getGroup( $group ) {
		if ( array_key_exists( $group, $this->poolGroups ) ) {
			return $this->poolGroups[ $group ];
		}

		return null;
	}
}
