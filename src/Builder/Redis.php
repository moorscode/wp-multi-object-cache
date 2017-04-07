<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Redis\RedisCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;
use Predis\Client;

class Redis implements PoolBuilderInterface {
	/**
	 * Creates the Redis Pool
	 *
	 * @param array $config Redis configuration.
	 *
	 * @return RedisCachePool
	 */
	public function create( array $config = [] ) {
		return new RedisCachePool( $this->initialize( $config ) );
	}

	/**
	 * Initializes the Redis object.
	 *
	 * @param array $config
	 *
	 * @return \Redis
	 */
	protected function initialize( array $config ) {
		$type = $this->getRedisType();

		$redis = new \Redis();

		switch ( $type ) {
			case 'hhvm':
				$this->connectHHVM( $redis, $config );
				break;

			case 'pecl':
				$this->connectPECL( $redis, $config );
				break;
		}

		$this->setExtensionConfiguration( $redis, $config );

		// Throws exception if Redis is unavailable.
		$redis->ping();

		return $redis;
	}

	/**
	 * Gets the Redis implementation type
	 *
	 * @return string
	 */
	private function getRedisType() {
		return defined( 'HHVM_VERSION' ) ? 'hhvm' : 'pecl';
	}

	/**
	 * Connects the HHVM Redis version.
	 *
	 * @param \Redis $redis
	 * @param array  $config
	 */
	private function connectHHVM( \Redis $redis, array $config ) {

		// Adjust host and port, if the scheme is `unix`
		if ( strcasecmp( 'unix', $config['scheme'] ) === 0 ) {
			$config['host'] = 'unix://' . $config['path'];
			$config['port'] = 0;
		}

		$redis->connect( $config['host'], $config['port'] );
	}

	/**
	 * Connects the PECL Redis version.
	 *
	 * @param \Redis $redis
	 * @param array  $config
	 */
	private function connectPECL( \Redis $redis, array $config ) {
		if ( strcasecmp( 'unix', $config['scheme'] ) === 0 ) {
			$redis->connect( $config['path'] );
		} else {
			$redis->connect( $config['host'], $config['port'] );
		}
	}

	/**
	 * Sets the extension variables based on the config.
	 *
	 * @param \Redis $redis
	 * @param array  $config
	 *
	 * @return void
	 */
	private function setExtensionConfiguration( \Redis $redis, array $config ) {
		if ( isset( $config['password'] ) ) {
			$redis->auth( $config['password'] );
		}

		if ( isset( $config['database'] ) ) {
			$redis->select( $config['database'] );
		}
	}
}
