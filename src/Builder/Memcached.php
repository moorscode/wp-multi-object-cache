<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Memcached\MemcachedCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilderInterface;

class Memcached implements PoolBuilderInterface {
	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return CacheItemPoolInterface
	 * @throws \Exception
	 */
	public function create( array $config = [] ) {
		$config = wp_parse_args( $config, [
			'server' => [ '127.0.0.1', 11211 ],
		] );

		$memcached = $this->createInstance( $config );
		$this->setOptions( $memcached, $config );

		if ( ! $this->addServers( $memcached, $this->getServers( $config ) ) ) {
			throw new \RuntimeException( 'Memcached failed to add servers to the configuration.' );
		}

		return new MemcachedCachePool( $memcached );
	}

	/**
	 * Creates the Memcached instance
	 *
	 * @param array $config Configuration supplied.
	 *
	 * @return \Memcached
	 */
	protected function createInstance( array $config ) {
		if ( ! empty( $config['persistent'] ) && is_string( $config['persistent'] ) ) {
			return new \Memcached( $config['persistent'] );
		}

		return new \Memcached();
	}

	/**
	 * Gets the servers from config
	 *
	 * @param array $config Configuration supplied.
	 *
	 * @return array
	 */
	protected function getServers( array $config ) {
		$servers = [];

		// Configuration supplied a single server.
		if ( ! empty( $config['server'] ) ) {
			$servers = [ $config['server'] ];
		}

		// Configuration supplied list of servers.
		if ( ! empty( $config['servers'] ) ) {
			$servers = $config['servers'];
		}

		return $servers;
	}

	/**
	 * Adds an array of servers to the pool.
	 *
	 * Each individual server in the array must include a domain and port, with an optional
	 * weight value: $servers = array( array( '127.0.0.1', 11211, 0 ) );
	 *
	 * @link    http://www.php.net/manual/en/memcached.addservers.php
	 *
	 * @param \Memcached $memcached Memcached instance.
	 * @param array      $servers   Array of server to register.
	 *
	 * @return bool True on success; false on failure.
	 */
	protected function addServers( \Memcached $memcached, array $servers ) {
		if ( $memcached->isPersistent() ) {
			$servers = $this->getUnusedServers( $memcached, $servers );
		}

		if ( ! empty( $servers ) ) {
			return $memcached->addServers( $servers );
		}

		// If Memcached is not persistent but we had no servers to add, return false.
		return $memcached->isPersistent();
	}

	/**
	 * Filters out used servers from a list
	 *
	 * @param \Memcached $memcached Memcached instance.
	 * @param array      $servers   List of servers to filter out used ones from.
	 *
	 * @return array List of unused server.
	 */
	protected function getUnusedServers( \Memcached $memcached, array $servers ) {
		$unused = $servers;

		$listed = $memcached->getServerList();
		if ( ! empty( $listed ) ) {
			// Reset unused to empty list.
			$unused = [];

			foreach ( $servers as $server ) {

				$test = [
					'host'   => $server[0],
					'port'   => isset( $server[1] ) ? (int) $server[1] : 11211,
					'weight' => isset( $server[2] ) ? (int) $server[2] : 0,
				];

				if ( in_array( $test, $listed, true ) ) {
					continue;
				}

				$unused[] = $server;
			}
		}

		return $unused;
	}

	/**
	 * Set options from configuration
	 *
	 * @param \Memcached $memcached
	 * @param array      $config
	 */
	private function setOptions( \Memcached $memcached, array $config ) {
		if ( empty( $config['options'] ) ) {
			return;
		}

		foreach ( $config['options'] as $key => $value ) {
			$memcached->setOption( $key, $value );
		}
	}
}
