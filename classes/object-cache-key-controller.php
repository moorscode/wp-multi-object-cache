<?php

/**
 * Class Object_Cache_Key_Controller
 *
 * Proxy class to easily format the key for any request
 */

class Object_Cache_Key_Controller implements Object_Cache_Controller_Interface {
    /** @var Object_Cache_Controller_Interface Controller */
    protected $controller;

    /** @var Object_Cache_Manager Manager */
    protected $manager;

    /**
     * Object_Cache_Key_Controller constructor.
     * @param Object_Cache_Controller_Interface $controller
     */
    public function __construct(Object_Cache_Controller_Interface $controller )
    {
        $this->controller = $controller;
    }

    protected function get_key($key) {
        return sprintf( Object_Cache_Manager::get_key_format(), $key );
    }

    /**
     * Adds data to the cache, if the cache key doesn't already exist.
     *
     * @param int|string $key The cache key to use for retrieval later.
     * @param mixed $data The data to add to the cache.
     * @param int $expire Optional. When the cache data should expire, in seconds.
     *                           Default 0 (no expiration).
     * @return bool False if cache key and group already exist, true on success.
     */
    public function add($key, $data, $expire)
    {
        $key = $this->get_key($key);
        return $this->controller->add($key, $data, $expire);
    }

    /**
     * Decrements numeric cache item's value.
     *
     * @param int|string $key The cache key to decrement.
     * @param int $offset Optional. The amount by which to decrement the item's value. Default 1.
     *
     * @return false|int False on failure, the item's new value on success.
     */
    public function decrease($key, $offset)
    {
        $key = $this->get_key($key);
        return $this->controller->decrease($key, $offset);
    }

    /**
     * Removes the cache contents matching key and group.
     *
     * @param int|string $key What the contents in the cache are called.
     *
     * @return bool True on successful removal, false on failure.
     */
    public function delete($key)
    {
        $key = $this->get_key($key);
        return $this->controller->delete($key);
    }

    /**
     * Removes all cache items.
     *
     * @return bool False on failure, true on success
     */
    public function flush()
    {
        return $this->controller->flush();
    }

    /**
     * Retrieves the cache contents from the cache by key and group.
     *
     * @param int|string $key The key under which the cache contents are stored.
     * @param bool $force Optional. Whether to force an update of the local cache from the persistent
     *                            cache. Default false.
     * @param bool $found Optional. Whether the key was found in the cache. Disambiguates a return of false,
     *                            a storable value. Passed by reference. Default null.
     * @return bool|mixed False on failure to retrieve contents or the cache
     *                      contents on success
     */
    public function get($key, $force = false, &$found = null)
    {
        $key = $this->get_key($key);
        return $this->controller->get($key, $force, $found);
    }

    /**
     * Increment numeric cache item's value
     *
     * @param int|string $key The key for the cache contents that should be incremented.
     * @param int $offset Optional. The amount by which to increment the item's value. Default 1.
     *
     * @return false|int False on failure, the item's new value on success.
     */
    public function increase($key, $offset = 1)
    {
        $key = $this->get_key($key);
        return $this->controller->increase($key, $offset);
    }

    /**
     * Replaces the contents of the cache with new data.
     *
     * @param int|string $key The key for the cache data that should be replaced.
     * @param mixed $data The new data to store in the cache.
     * @param int $expire Optional. When to expire the cache contents, in seconds.
     *                           Default 0 (no expiration).
     *
     * @return bool False if original value does not exist, true if contents were replaced
     */
    public function replace($key, $data, $expire = 0)
    {
        $key = $this->get_key($key);
        return $this->controller->replace($key, $data, $expire);
    }

    /**
     * Saves the data to the cache.
     *
     * Differs from wp_cache_add() and wp_cache_replace() in that it will always write data.
     *
     * @param int|string $key The cache key to use for retrieval later.
     * @param mixed $data The contents to store in the cache.
     *                           to be used across groups. Default empty.
     * @param int $expire Optional. When to expire the cache contents, in seconds.
     *                           Default 0 (no expiration).
     *
     * @return bool False on failure, true on success
     */
    public function set($key, $data, $expire = 0)
    {
        $key = $this->get_key($key);
        return $this->controller->set($key, $data, $expire);
    }
}
