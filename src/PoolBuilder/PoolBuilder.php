<?php

namespace WPMultiObjectCache\PoolBuilder;

use Cache\Adapter\Common\AbstractCachePool;

interface PoolBuilder
{
    /**
     * Creates a pool
     *
     * @param array $config Config to use to create the pool.
     *
     * @return AbstractCachePool
     */
    public function create(array $config = []);
}
