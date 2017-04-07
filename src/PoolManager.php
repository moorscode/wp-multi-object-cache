<?php

namespace MultiObjectCache\Cache;

use Psr\Cache\CacheItemPoolInterface;

class PoolManager {
	/** @var array */
	protected $pools = array();

	/** @var PoolGroupConnectorInterface Pool Group Connector */
	protected $pool_group_connector;

	/** @var PoolFactoryInterface Pool Factory */
	protected $pool_factory;

	/**
	 * PoolManager constructor.
	 *
	 * @param PoolGroupConnectorInterface $pool_group_connector Pool Group connector instance.
	 * @param PoolFactoryInterface        $pool_factory         Pool Factory instance.
	 */
	public function __construct( PoolGroupConnectorInterface $pool_group_connector, PoolFactoryInterface $pool_factory ) {
		$this->pool_group_connector = $pool_group_connector;
		$this->pool_factory         = $pool_factory;
	}

	/**
	 * Reads configuration
	 * @throws \Exception
	 */
	public function initialize() {
		require_once dirname( __DIR__ ) . '/config/object-cache.config.php';

		/** @var array $config */
		$this->register_pools( $config['pools'] );
	}

	/**
	 * Gets the controller for a group.
	 *
	 * @param string $group Group to get controller for.
	 *
	 * @return PSRCacheAdapter
	 */
	public function get( $group = '' ) {
		$pool = $this->pool_group_connector->get( $group );

		// Create a new Key Pool with initial group name.
		return new PSRCacheAdapter( $pool, $group );
	}

	/**
	 * Gets all registered pools.
	 *
	 * @return CacheItemPoolInterface[]
	 */
	public function get_pools() {
		return $this->pools;
	}

	/**
	 * Registers the pools for the groups they specified.
	 *
	 * @param array $pools List of pools to load.
	 *
	 * @throws \Exception
	 */
	protected function register_pools( $pools ) {
		// Register pools.
		foreach ( $pools as $pool => $data ) {
			$this->register_pool( $pool, $data );
		}
	}

	/**
	 * Registers a pool.
	 *
	 * @param string $pool_type Class name of the Pool to register.
	 * @param array  $data      Configuration to use on the pool.
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function register_pool( $pool_type, $data ) {
		if ( ! class_exists( $pool_type ) ) {
			throw new \InvalidArgumentException( sprintf( 'Class %s not found while loading Object Cache pools.',
				$pool_type ) );
		}

		if ( ! is_array( $data['groups'] ) || 0 === count( $data['groups'] ) ) {
			throw new \InvalidArgumentException( sprintf( 'The pool %s must have at least one group definition.',
				$pool_type ) );
		}

		if ( $this->check_prerequisites( $data['prerequisites'] ) ) {
			$args                      = ( isset( $data['config'] ) ? $data['config'] : null );
			$this->pools[ $pool_type ] = $this->pool_factory->get( $pool_type, $args );
		} else {
			trigger_error( 'Pool prerequisites not met, using Null implementation.', E_USER_WARNING );
		}

		foreach ( $data['groups'] as $group ) {
			$this->pool_group_connector->add( $this->pools[ $pool_type ], $group );
		}
	}

	/**
	 * Checks for all prerequisites
	 *
	 * @param array $prerequisites Prerequisites to check.
	 *
	 * @return bool
	 */
	protected function check_prerequisites( array $prerequisites = array() ) {
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
