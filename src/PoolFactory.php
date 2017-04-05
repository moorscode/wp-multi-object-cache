<?php

namespace MultiObjectCache\Cache;

class PoolFactory {
	/**
	 * @param $type
	 * @param array $config
	 *
	 * @return mixed
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
