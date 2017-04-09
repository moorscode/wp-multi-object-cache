<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Predis\PredisCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilderInterface;
use Predis\Client;

class Predis implements PoolBuilderInterface {
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
			'scheme' => 'tcp',
			'port' => 6379,
		] );

		return new PredisCachePool( $this->initialize( $config ) );
	}

	/**
	 * Initializes the Predis client.
	 *
	 * @param array $config Configuration.
	 *
	 * @return Client
	 */
	private function initialize( array $config ) {
		$options    = [];
		$parameters = $config;

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
