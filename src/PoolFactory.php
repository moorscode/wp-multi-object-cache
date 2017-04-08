<?php

namespace WPMultiObjectCache;

class PoolFactory implements PoolFactoryInterface {
	/**
	 * Gets a pool by type and configuration
	 *
	 * @param string $type   Type of Pool to retrieve.
	 * @param array  $config Optional. Configuration for creating the Pool.
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function get( $type, array $config = array() ) {
		$class_name = __NAMESPACE__ . '\\Builder\\' . $type;
		if ( ! class_exists( $class_name ) ) {
			throw new \InvalidArgumentException( sprintf( 'Builder %s does not exist.', $type ) );
		}

		/** @var PoolBuilderInterface $builder */
		$builder = new $class_name();

		return $builder->create( $config );
	}
}
