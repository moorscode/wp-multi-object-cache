<?php

namespace WPMultiObjectCache;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

class PoolFactory implements PoolFactoryInterface {
	/** @var AdminNotifier Admin Notifier */
	protected $adminNotifier;

	/**
	 * PoolFactory constructor.
	 *
	 * @param AdminNotifier $adminNotifier
	 */
	public function __construct( AdminNotifier $adminNotifier ) {
		$this->adminNotifier = $adminNotifier;
	}

	/**
	 * Gets a pool by type and configuration
	 *
	 * @param string $type   Type of Pool to retrieve.
	 * @param array  $config Optional. Configuration for creating the Pool.
	 *
	 * @return CacheItemPoolInterface
	 * @throws \LogicException
	 */
	public function get( $type, array $config = array() ) {
		$class_name = __NAMESPACE__ . '\\Builder\\' . $type;

		try {
			if ( ! class_exists( $class_name ) ) {
				throw new \LogicException( sprintf( 'Builder %s does not exist.', $type ) );
			}

			/** @var PoolBuilderInterface $builder */
			$builder = new $class_name();

			$pool = $builder->create( $config );
		} catch ( \Exception $e ) {
			$message = sprintf( '%s Cache Builder problem occurred: ' . $e->getMessage() . '. Reverting to non-persistent cache.', $type );
			$this->adminNotifier->add( new AdminNotification( new AdminNotificationTypeError(), $message ) );

			$pool = $this->getFallbackPool();
		}

		return $pool;
	}

	/**
	 * Creates a Void pool.
	 *
	 * @return CacheItemPoolInterface
	 */
	public function getFallbackPool() {
		static $fallbackPool;

		if ( null === $fallbackPool ) {
			$fallbackPool = new ArrayCachePool();
		}

		return $fallbackPool;
	}
}
