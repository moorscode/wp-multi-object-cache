<?php

namespace MultiObjectCache\Cache\Builder;

use Cache\Adapter\Redis\RedisCachePool;
use MultiObjectCache\Cache\PoolBuilderInterface;
use Predis\Client;

class Redis implements PoolBuilderInterface {
	/** @var array Configuration */
	private $config = [];

	/**
	 * @param array $config
	 *
	 * @return RedisCachePool
	 */
	public function create( array $config = array() ) {
		$this->config = $config;

		$redis = $this->initialize();

		return new RedisCachePool( $redis );
	}

	/**
	 * @return Client|\Redis
	 */
	protected function initialize() {
		$type = $this->getRedisType();

		$redis = new \Redis();

		switch ( $type ) {
			case 'hhvm':
				$this->connectHHVM( $redis );
				break;

			case 'pecl':
				$this->connectPECL( $redis );
				break;
		}

		$this->setExtensionConfiguration( $redis );

		// Throws exception if Redis is unavailable.
		$redis->ping();

		return $redis;
	}

	/**
	 * @return string
	 */
	private function getRedisType() {
		return defined( 'HHVM_VERSION' ) ? 'hhvm' : 'pecl';
	}

	/**
	 * @param \Redis $redis
	 *
	 * @return void
	 */
	private function connectHHVM( $redis ) {

		// Adjust host and port, if the scheme is `unix`
		if ( strcasecmp( 'unix', $this->config['scheme'] ) === 0 ) {
			$this->config['host'] = 'unix://' . $this->config['path'];
			$this->config['port'] = 0;
		}

		$redis->connect( $this->config['host'], $this->config['port'] );
	}

	/**
	 * @param \Redis $redis
	 *
	 * @return void
	 */
	private function connectPECL( $redis ) {
		if ( strcasecmp( 'unix', $this->config['scheme'] ) === 0 ) {
			$redis->connect( $this->config['path'] );
		} else {
			$redis->connect( $this->config['host'], $this->config['port'] );
		}
	}

	/**
	 * @return void
	 */
	private function setExtensionConfiguration( $redis ) {
		if ( isset( $this->config['password'] ) ) {
			$redis->auth( $this->config['password'] );
		}

		if ( isset( $this->config['database'] ) ) {
			$redis->select( $this->config['database'] );
		}
	}
}
