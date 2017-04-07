<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Memcached\MemcachedCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;

class Memcached implements PoolBuilderInterface {
	/** @var \Memcached Memcached instance */
	protected $memcached;

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 */
	public function create( array $config = [] ) {
		$this->memcached = $this->createInstance( $config );

		$this->addServers( $this->getServers( $config ) );

		return new MemcachedCachePool( $this->memcached );
	}

	/**
	 * Adds an array of servers to the pool.
	 *
	 * Each individual server in the array must include a domain and port, with an optional
	 * weight value: $servers = array( array( '127.0.0.1', 11211, 0 ) );
	 *
	 * @link    http://www.php.net/manual/en/memcached.addservers.php
	 *
	 * @param   array $servers Array of server to register.
	 *
	 * @return  bool True on success; false on failure.
	 */
	protected function addServers( array $servers ) {
		$add = $servers;

		if ( $this->memcached->isPersistent() ) {
			$add = $this->getUnusedServers( $servers );
		}

		if ( ! empty( $add ) ) {
			return $this->memcached->addServers( $add );
		}

		return true;
	}

	/**
	 * Filters out used servers from a list
	 *
	 * @param array $servers List of servers to filter out used ones from.
	 *
	 * @return array List of unused server.
	 */
	private function getUnusedServers( array $servers ) {
		$unused = $servers;

		$listed = $this->memcached->getServerList();
		if ( ! empty( $listed ) ) {
			$unused = array();

			foreach ( $servers as $server ) {

				$test = array(
					'host'   => $server[0],
					'port'   => $server[1],
					'weight' => isset( $server[2] ) ? (int) $server[2] : 0,
				);

				$found = in_array( $test, $listed, true );

				if ( $found ) {
					continue;
				}

				$unused[] = $server;
			}
		}

		return $unused;
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
		$servers = $config['servers'];
		if ( empty( $servers ) ) {
			$servers = [ [ '127.0.0.1', 11211 ] ];
		}

		return $servers;
	}
}
