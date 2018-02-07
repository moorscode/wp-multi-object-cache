<?php

namespace WPMultiObjectCache\PoolBuilder;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;

class PHP implements PoolBuilder
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
        return new ArrayCachePool();
    }
}
