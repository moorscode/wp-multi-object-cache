<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Predis\PredisCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;
use Predis\Client;

class Predis implements PoolBuilderInterface {
	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 * @throws \Exception
	 */
	public function create( array $config = [] ) {

		$predis = $this->initialize( $config );

		return new PredisCachePool( $predis );
	}

	/**
	 * Initializes the Predis client.
	 *
	 * @param array $config
	 *
	 * @return Client
	 */
	private function initialize( array $config ) {
		$options    = [];
		$parameters = null;

		if ( ! empty( $config['cluster'] ) ) {
			$parameters         = $config['cluster'];
			$options['cluster'] = 'redis';
		}

		if ( ! empty( $config['servers'] ) ) {
			$parameters             = $config['servers'];
			$options['replication'] = true;
		}

		if ( null !== $parameters && ! empty( $config['password'] ) ) {
			$options['parameters']['password'] = $config['password'];
		}

		$redis = new Client( $parameters, $options );
		$redis->connect();

		return $redis;
	}
}
