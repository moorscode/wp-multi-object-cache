<?php

namespace WPMultiObjectCache;

interface PoolFactoryInterface {
	/**
	 * Gets a pool by type and configuration
	 *
	 * @param string $type   Type of Pool to retrieve.
	 * @param array  $config Optional. Configuration for creating the Pool.
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function get( $type, array $config = array() );
}
