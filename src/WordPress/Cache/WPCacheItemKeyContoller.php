<?php

namespace WordPress\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Class Object_Cache_Key_pool
 *
 * @todo remove this class architecturally
 *
 * Proxy class to easily format the key for any request
 */
class WPCacheItemKeyContoller implements WPCacheItemPoolInterface {
	/** @var WPCacheItemPoolInterface Pool */
	protected $pool;
	/** @var string Group */
	protected $group;

	/**
	 * Object_Cache_Key_pool constructor.
	 *
	 * @param WPCacheItemPoolInterface $pool
	 * @param string $group The group that is being requested
	 */
	public function __construct( WPCacheItemPoolInterface $pool, $group ) {
		$this->pool = $pool;
		$this->group      = $group;
	}

	/**
	 * Builds the unique cache key for the contextual request.
	 *
	 * @todo should be a trait?
	 *
	 * @param string $key The requested key.
	 *
	 * @return string
	 */
	protected function get_key( $key ) {
		$prefix = '';

		if ( ! empty( $this->group ) ) {
			$prefix = $this->group . ':';
		}

		return sprintf( Manager::get_key_format(), $prefix . $key );
	}

	/**
	 * Adds data to the cache, if the cache key does not already exist.
	 *
	 * @param int|string $key The cache key to use for retrieval later.
	 * @param mixed $data The data to add to the cache.
	 * @param int $expire Optional. When the cache data should expire, in seconds.
	 *                           Default 0 (no expiration).
	 *
	 * @return bool False if cache key and group already exist, true on success.
	 */
	public function add( $key, $data, $args = null ) {
		return $this->pool->add( $this->get_key( $key ), $data, $args );
	}

	/**
	 * Decrements numeric cache item's value.
	 *
	 * @param int|string $key The cache key to decrement.
	 * @param int $offset Optional. The amount by which to decrement the item's value. Default 1.
	 *
	 * @return false|int False on failure, the item's new value on success.
	 */
	public function decrease( $key, $offset, $args = null ) {
		return $this->pool->decrease( $this->get_key( $key ), $offset, $args );
	}

	/**
	 * Removes the cache contents matching key and group.
	 *
	 * @param int|string $key What the contents in the cache are called.
	 *
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete( $key, $args = null ) {
		return $this->pool->delete( $this->get_key( $key ), $args );
	}

	/**
	 * Removes all cache items.
	 *
	 * @return bool False on failure, true on success
	 */
	public function clear() {
		return $this->pool->clear();
	}

	/**
	 * Retrieves the cache contents from the cache by key and group.
	 *
	 * @param int|string $key The key under which the cache contents are stored.
	 * @param bool $force Optional. Whether to force an update of the local cache from the persistent
	 *                            cache. Default false.
	 * @param bool $found Optional. Whether the key was found in the cache. Disambiguates a return of false,
	 *                            a storable value. Passed by reference. Default null.
	 *
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *                      contents on success
	 */
	public function get( $key, $force = false, &$found = null, $args = null ) {
		return $this->pool->get( $this->get_key( $key ), $force, $found, $args );
	}

	/**
	 * Increment numeric cache item's value
	 *
	 * @param int|string $key The key for the cache contents that should be incremented.
	 * @param int $offset Optional. The amount by which to increment the item's value. Default 1.
	 *
	 * @return false|int False on failure, the item's new value on success.
	 */
	public function increase( $key, $offset = 1, $args = null ) {
		return $this->pool->increase( $this->get_key( $key ), $offset, $args );
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
	public function replace( $key, $data, $args = null ) {
		return $this->pool->replace( $this->get_key( $key ), $data, $args );
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
	public function set( $key, $data, $args = null ) {
		return $this->pool->set( $this->get_key( $key ), $data, $args );
	}

	/**
	 * Returns a Cache Item representing the specified key.
	 *
	 * This method must always return a CacheItemInterface object, even in case of
	 * a cache miss. It MUST NOT return null.
	 *
	 * @param string $key
	 *   The key for which to return the corresponding Cache Item.
	 *
	 * @throws InvalidArgumentException
	 *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
	 *   MUST be thrown.
	 *
	 * @return CacheItemInterface
	 *   The corresponding Cache Item.
	 */
	public function getItem( $key ) {
		// TODO: Implement getItem() method.
	}

	/**
	 * Returns a traversable set of cache items.
	 *
	 * @param string[] $keys
	 *   An indexed array of keys of items to retrieve.
	 *
	 * @throws InvalidArgumentException
	 *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
	 *   MUST be thrown.
	 *
	 * @return array|\Traversable
	 *   A traversable collection of Cache Items keyed by the cache keys of
	 *   each item. A Cache item will be returned for each key, even if that
	 *   key is not found. However, if no keys are specified then an empty
	 *   traversable MUST be returned instead.
	 */
	public function getItems( array $keys = array() ) {
		// TODO: Implement getItems() method.
	}

	/**
	 * Confirms if the cache contains specified cache item.
	 *
	 * Note: This method MAY avoid retrieving the cached value for performance reasons.
	 * This could result in a race condition with CacheItemInterface::get(). To avoid
	 * such situation use CacheItemInterface::isHit() instead.
	 *
	 * @param string $key
	 *   The key for which to check existence.
	 *
	 * @throws InvalidArgumentException
	 *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
	 *   MUST be thrown.
	 *
	 * @return bool
	 *   True if item exists in the cache, false otherwise.
	 */
	public function hasItem( $key ) {
		// TODO: Implement hasItem() method.
	}

	/**
	 * Removes the item from the pool.
	 *
	 * @param string $key
	 *   The key to delete.
	 *
	 * @throws InvalidArgumentException
	 *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
	 *   MUST be thrown.
	 *
	 * @return bool
	 *   True if the item was successfully removed. False if there was an error.
	 */
	public function deleteItem( $key ) {
		// TODO: Implement deleteItem() method.
	}

	/**
	 * Removes multiple items from the pool.
	 *
	 * @param string[] $keys
	 *   An array of keys that should be removed from the pool.
	 *
	 * @throws InvalidArgumentException
	 *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
	 *   MUST be thrown.
	 *
	 * @return bool
	 *   True if the items were successfully removed. False if there was an error.
	 */
	public function deleteItems( array $keys ) {
		// TODO: Implement deleteItems() method.
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param CacheItemInterface $item
	 *   The cache item to save.
	 *
	 * @return bool
	 *   True if the item was successfully persisted. False if there was an error.
	 */
	public function save( CacheItemInterface $item ) {
		// TODO: Implement save() method.
	}

	/**
	 * Sets a cache item to be persisted later.
	 *
	 * @param CacheItemInterface $item
	 *   The cache item to save.
	 *
	 * @return bool
	 *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
	 */
	public function saveDeferred( CacheItemInterface $item ) {
		// TODO: Implement saveDeferred() method.
	}

	/**
	 * Persists any deferred cache items.
	 *
	 * @return bool
	 *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
	 */
	public function commit() {
		// TODO: Implement commit() method.
	}
}
