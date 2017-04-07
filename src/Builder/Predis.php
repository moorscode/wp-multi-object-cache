<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Predis\PredisCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;
use Predis\Client;

class Predis implements PoolBuilderInterface {
	/** @var array Configuration */
	protected $config = [];

	/**
	 * Creates a pool
	 *
	 * @param array $config Config to use to create the pool.
	 *
	 * @return AbstractCachePool
	 * @throws \Exception
	 */
	public function create( array $config = array() ) {
		$this->config = $config;

		$predis = $this->connect();

		return new PredisCachePool( $predis );
	}

	/**
	 * @return Client
	 * @throws \Exception
	 */
	private function connect() {
		$options    = array();
		$parameters = null;

		if ( ! empty( $this->config['cluster'] ) ) {
			$parameters         = $this->config['cluster'];
			$options['cluster'] = 'redis';
		}

		if ( ! empty( $this->config['servers'] ) ) {
			$parameters             = $this->config['servers'];
			$options['replication'] = true;
		}

		if ( null !== $parameters && ! empty( $this->config['password'] ) ) {
			$options['parameters']['password'] = $this->config['password'];
		}

		$redis = new Client( $parameters, $options );
		$redis->connect();

		return $redis;
	}
}
