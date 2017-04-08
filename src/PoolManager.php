<?php

namespace MultiObjectCache\Cache;

use Psr\Cache\CacheItemPoolInterface;

class PoolManager {
	/** @var array */
	protected $pools = array();

	/** @var PoolGroupConnectorInterface Pool Group Connector */
	protected $poolGroupConnector;

	/** @var PoolFactoryInterface Pool Factory */
	protected $poolFactory;

	/**
	 * PoolManager constructor.
	 *
	 * @param PoolGroupConnectorInterface $poolGroupConnector Pool Group connector instance.
	 * @param PoolFactoryInterface        $poolFactory        Pool Factory instance.
	 */
	public function __construct( PoolGroupConnectorInterface $poolGroupConnector, PoolFactoryInterface $poolFactory ) {
		$this->poolGroupConnector = $poolGroupConnector;
		$this->poolFactory        = $poolFactory;
	}

	/**
	 * Reads configuration
	 * @throws \Exception
	 */
	public function initialize() {
		require_once dirname( __DIR__ ) . '/config/object-cache.config.php';

		/** @var array $config */
		$this->registerPools( $config['pools'] );
	}

	/**
	 * Gets the controller for a group.
	 *
	 * @param string $group Group to get controller for.
	 *
	 * @return PSRCacheAdapter
	 */
	public function get( $group = '' ) {
		// Create a new Key Pool with initial group name.
		return new PSRCacheAdapter( $this->poolGroupConnector->get( $group ), $group );
	}

	/**
	 * Gets all registered pools.
	 *
	 * @return CacheItemPoolInterface[]
	 */
	public function getPools() {
		return $this->pools;
	}

	/**
	 * Registers the pools for the groups they specified.
	 *
	 * @param array $pools List of pools to load.
	 *
	 * @throws \Exception
	 */
	protected function registerPools( $pools ) {
		// Register pools.
		foreach ( $pools as $name => $data ) {
			$this->registerPool( $name, $data );
		}
	}

	/**
	 * Registers a pool.
	 *
	 * @param string $name Class name of the Pool to register.
	 * @param array  $data Configuration to use on the pool.
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerPool( $name, $data ) {
		if ( ! is_array( $data['groups'] ) || 0 === count( $data['groups'] ) ) {
			throw new \InvalidArgumentException( sprintf( 'The pool %s must have at least one group definition.',
				$name ) );
		}

		if ( empty( $data['prerequisites'] ) || $this->checkPrerequisites( $data['prerequisites'] ) ) {
			$args                 = ( isset( $data['config'] ) ? $data['config'] : [] );
			$this->pools[ $name ] = $this->poolFactory->get( $data['method'], $args );
		} else {
			trigger_error( 'Pool prerequisites not met, using Null implementation.', E_USER_WARNING );
		}

		foreach ( $data['groups'] as $group ) {
			$this->poolGroupConnector->add( $this->pools[ $name ], $group );
		}
	}

	/**
	 * Checks for all prerequisites
	 *
	 * @param array $prerequisites Prerequisites to check.
	 *
	 * @return bool
	 */
	protected function checkPrerequisites( array $prerequisites = array() ) {
		$met = true;

		foreach ( $prerequisites as $prerequisite ) {
			switch ( $prerequisite ) {
				case 'class':
					$met = class_exists( $prerequisite ) && $met;
					if ( ! $met ) {
						return false;
					}
					break;
				case 'function':
					$met = function_exists( $prerequisite ) && $met;
					if ( ! $met ) {
						return false;
					}
					break;
			}
		}

		return $met;
	}
}
