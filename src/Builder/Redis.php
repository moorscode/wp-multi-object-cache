<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Redis\RedisCachePool;
use WPMultiObjectCache\PoolBuilderInterface;
use Predis\Client;

class Redis implements PoolBuilderInterface {
	/**
	 * Creates the Redis Pool
	 *
	 * @param array $config Redis configuration.
	 *
	 * @return RedisCachePool
	 *
	 * @throws \RuntimeException
	 */
	public function create( array $config = [] ) {
		$config = wp_parse_args( $config, [
			'scheme' => 'tcp',
			'port'   => 6379,
		] );

		return new RedisCachePool( $this->initialize( $config ) );
	}

	/**
	 * Initializes the Redis object.
	 *
	 * @param array $config Configuration.
	 *
	 * @return \Redis
	 * @throws \RuntimeException
	 */
	protected function initialize( array $config ) {
		$connected = false;

		$type = $this->getRedisType();
		$redis = new \Redis();

		switch ( $type ) {
			case 'hhvm':
				$connected = $this->connectHHVM( $redis, $config );
				break;

			case 'pecl':
				$connected = $this->connectPECL( $redis, $config );
				break;
		}

		if ( ! $connected ) {
			throw new \RuntimeException( 'Redis could not connect.' );
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
	 * @param \Redis $redis  Redis instance.
	 * @param array  $config Configuration.
	 *
	 * @return bool
	 */
	private function connectHHVM( \Redis $redis, array $config ) {

		// Adjust host and port, if the scheme is `unix`
		if ( strcasecmp( 'unix', $config['scheme'] ) === 0 ) {
			$config['host'] = 'unix://' . $config['path'];
			$config['port'] = 0;
		}

		return $redis->connect( $config['host'], $config['port'] );
	}

	/**
	 * Connects the PECL Redis version.
	 *
	 * @param \Redis $redis  Redis instance.
	 * @param array  $config Configuration.
	 *
	 * @return bool
	 */
	private function connectPECL( \Redis $redis, array $config ) {
		if ( strcasecmp( 'unix', $config['scheme'] ) === 0 ) {
			return $redis->connect( $config['path'] );
		}

		return $redis->connect( $config['host'], $config['port'] );
	}

	/**
	 * Sets the extension variables based on the config.
	 *
	 * @param \Redis $redis  Redis instance.
	 * @param array  $config Configuration.
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
