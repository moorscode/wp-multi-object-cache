<?php

namespace WPMultiObjectCache\Builder;

use Cache\Adapter\Void\VoidCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilderInterface;

class Null implements PoolBuilderInterface
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
