<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Memcache\MemcacheCachePool;
use WPMultiObjectCache\PoolBuilderInterface;

class Memcache implements PoolBuilderInterface {

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = [] ) {
		$memcache = new \Memcache();

		$this->initialize( $memcache, $config );

		return new MemcacheCachePool( $memcache );
	}

	/**
	 * Initializes the Memcache connection.
	 *
	 * @param \Memcache $memcache Memcache instance.
	 * @param array     $config   Configuration.
	 *
	 * @return bool
	 */
	private function initialize( $memcache, $config ) {
		if ( $config['persistent'] ) {
			return call_user_func_array( [ $memcache, 'pconnect' ], $config['server'] );
		}

		$servers = [];
		if ( ! empty( $config['server'] ) ) {
			$servers = [ $config['server'] ];
		}

		if ( ! empty( $config['servers'] ) ) {
			$servers = $config['servers'];
		}

		$results = array_map( function ( $server ) use ( $memcache ) {
			call_user_func_array( [ $memcache, 'addServer' ], $server );
		}, $servers );

		$success = array_filter( $results );

		return ( count( $success ) === count( $results ) );
	}
}
