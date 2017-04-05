<?php

namespace MultiObjectCache\Cache;

use Cache\Adapter\Void\VoidCachePool;

class PoolFactory {
	/** @var PoolBuilderInterface[] Builder */
	protected $builders = [];

	public function register_builder( PoolBuilderInterface $builder, $type ) {
		$this->builders[ $type ] = $builder;
	}

	public function get( $type, array $config = array() ) {
		if ( ! isset( $this->builders[ $type ] ) ) {
			return new VoidCachePool();
		}

		return $this->builders[ $type ]->create( $config );
	}
}
