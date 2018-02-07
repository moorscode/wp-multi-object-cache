<?php

namespace WPMultiObjectCache;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WPMultiObjectCache\PoolBuilder\PoolBuilder;

class PoolFactory
{
    /**
     * @var Admin\Notifier Admin Notifier
     */
    protected $adminNotifier;

    /**
     * PoolFactory constructor.
     *
     * @param Admin\Notifier $adminNotifier
     */
    public function __construct(Admin\Notifier $adminNotifier)
    {
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
    public function get($type, array $config = array())
    {
        $class_name = __NAMESPACE__ . '\\PoolBuilder\\' . $type;

        try {
            if (! class_exists($class_name)) {
                throw new \LogicException(sprintf('PoolBuilder %s does not exist.', $type));
            }

            /**
             * @var PoolBuilder $builder
             */
            $builder = new $class_name();

            $pool = $builder->create($config);
        } catch (\Exception $e) {
            $message = sprintf(
                '%s Cache Builder problem occurred: ' . $e->getMessage() . '. Reverting to non-persistent cache.',
                $type
            );
            $this->adminNotifier->add(new Admin\Notification('error', $message));

            $pool = $this->getFallbackPool();
        }

        return $pool;
    }

    /**
     * Creates a Void pool.
     *
     * @return CacheItemPoolInterface
     */
    public function getFallbackPool()
    {
        static $fallbackPool;

        if (null === $fallbackPool) {
            $fallbackPool = new ArrayCachePool();
        }

        return $fallbackPool;
    }
}
