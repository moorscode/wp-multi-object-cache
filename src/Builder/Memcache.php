<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Memcache\MemcacheCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilderInterface;

class Memcache implements PoolBuilderInterface {

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return CacheItemPoolInterface
	 * @throws \RuntimeException
	 */
	public function create( array $config = [] ) {
		$config = wp_parse_args( $config, [
			'server' => [ '127.0.0.1', 11211 ],
		] );

		$memcache = new \Memcache();

		if ( ! $this->initialize( $memcache, $config ) ) {
			throw new \RuntimeException( 'Memcache failed to add servers to it\'s pool.' );
		}

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
