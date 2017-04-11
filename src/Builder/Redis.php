<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Redis\RedisCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\Manager;
use WPMultiObjectCache\PoolBuilderInterface;

class Redis implements PoolBuilderInterface {
	/**
	 * Creates the Redis Pool
	 *
	 * @param array $config Redis configuration.
	 *
	 * @return CacheItemPoolInterface
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
		if ( ! class_exists( 'Redis' ) ) {
			throw new \RuntimeException( 'The Redis class could not be found, please install the PHP Redis extension' );
		}

		$type  = $this->getRedisType();
		$redis = new \Redis();

		Manager::convertWarningToException();

		switch ( $type ) {
			case 'hhvm':
				$this->connectHHVM( $redis, $config );
				break;

			case 'pecl':
				$this->connectPECL( $redis, $config );
				break;
		}

		Manager::restoreErrorHandling();

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
