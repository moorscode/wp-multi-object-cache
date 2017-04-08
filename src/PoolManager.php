<?php

namespace WPMultiObjectCache;

use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\Builder\Void;

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
	 * @throws \LogicException
	 */
	protected function registerPool( $name, $data ) {
		if ( empty( $data['method'] ) ) {
			throw new \LogicException( sprintf( 'The pool %s must have a "method" defined.', $name ) );
		}

		if ( ! is_array( $data['groups'] ) || 0 === count( $data['groups'] ) ) {
			throw new \LogicException( sprintf( 'The pool %s must have at least one group definition.',
				$name ) );
		}

		$this->pools[ $name ] = $this->createPool( $data );

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
		foreach ( $prerequisites as $prerequisite ) {
			switch ( $prerequisite ) {
				case 'class':
					if ( ! class_exists( $prerequisite ) ) {
						return false;
					}
					break;

				case 'function':
					if ( ! function_exists( $prerequisite ) ) {
						return false;
					}
					break;
			}
		}

		return true;
	}

	/**
	 * Create a pool from configuration.
	 *
	 * @param array $data Configuration data to use.
	 *
	 * @return PoolBuilderInterface
	 * @throws \LogicException
	 */
	protected function createPool( $data ) {
		if ( empty( $data['prerequisites'] ) || $this->checkPrerequisites( $data['prerequisites'] ) ) {
			$args = ( isset( $data['config'] ) ? $data['config'] : [] );

			return $this->poolFactory->get( $data['method'], $args );
		}

		trigger_error( 'Pool prerequisites not met, using Void (Null) implementation.', E_USER_WARNING );

		return new Void();
	}
}
