<?php

namespace WPMultiObjectCache;

use Cache\Adapter\Void\VoidCachePool;
use Psr\Cache\CacheItemPoolInterface;

class PoolFactory implements PoolFactoryInterface {
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

		if ( ! class_exists( $class_name ) ) {
			throw new \LogicException( sprintf( 'Builder %s does not exist.', $type ) );
		}

		/** @var PoolBuilderInterface $builder */
		$builder = new $class_name();

		try {
			$pool = $builder->create( $config );
		} catch( \Exception $e ) {
			$pool = new VoidCachePool();
		}

		return $pool;
	}
}
