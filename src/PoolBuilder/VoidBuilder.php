<?php

namespace WPMultiObjectCache\PoolBuilder;

use Cache\Adapter\Void\VoidCachePool;
use Psr\Cache\CacheItemPoolInterface;

class VoidBuilder implements PoolBuilder
{

    /**
     * Creates a pool
     *
     * @param array $config Config to use to create the pool.
     *
     * @return CacheItemPoolInterface
     */
    public function create(array $config = [])
    {
        return new VoidCachePool();
    }
}
