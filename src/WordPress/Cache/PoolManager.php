<?php

namespace WordPress\Cache;

use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;

class PoolManager {
	/** @var  array */
	protected $pools = array();

	/** @var PoolGroupConnector Pool Group Connector */
	protected $pool_group_connector;

	public function __construct( PoolGroupConnector $pool_group_connector ) {
		$this->pool_group_connector = $pool_group_connector;
	}

	/**
	 * Reads configuration
	 * @throws \Exception
	 */
	public function initialize() {
		// @todo extract configuration
		// read configuration
		$config = array(
			'pools' => array(
				// Default/fallback controller.
				'\WordPress\Cache\Pool\Redis'         => array(
					'config' => array(
						'servers' => array(
							'ip'   => '127.0.0.1',
							'port' => '1112'
						),
					),
					'groups' => array(
						''
					)
				),
				// Use Memcached controller for transients.
				'\WordPress\Cache\Pool\Memcache'      => array(
					'config' => array(
						'servers' => array(
							'ip'   => '127.0.0.1',
							'port' => '1112'
						),
					),
					'groups' => array(
						'site-transient'
					),
				),
				// Use Non Persistent Pool.
				'\WordPress\Cache\Pool\NonPersistent' => array(
					'groups' => array(
						'non-persistent'
					),
				),
			)
		);

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
		$pool = $this->pool_group_connector->get_pool( $group );

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
	 * @param string $pool Class name of the Pool to register.
	 * @param array  $data Configuration to use on the pool.
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function register_pool( $pool, $data ) {
		if ( ! class_exists( $pool ) ) {
			// Throw exception.
			throw new \InvalidArgumentException( sprintf( 'Class %s not found while loading Object Cache pools.', $pool ) );
		}

		if ( empty( $data['groups'] ) ) {
			throw new \InvalidArgumentException( sprintf( 'The pool %s must have at least one group definition.', $pool ) );
		}

		$args = ( isset( $data['config'] ) ? $data['config'] : null );

		$this->pools[ $pool ] = $this->get_pool_instance( $pool, $args );

		foreach ( $data['groups'] as $group ) {
			$this->pool_group_connector->add( $this->pools[ $pool ], $group );
		}
	}

	/**
	 * Gets the Pool instance.
	 *
	 * @param string $pool Class name of the pool to instance.
	 * @param array  $args Optional. Class arguments.
	 *
	 * @return object
	 */
	protected function get_pool_instance( $pool, $args = null ) {
		$reflection_class = new ReflectionClass( $pool );

		if ( null !== $args ) {
			return $reflection_class->newInstanceArgs( $args );
		}

		return $reflection_class->newInstance();
	}
}
