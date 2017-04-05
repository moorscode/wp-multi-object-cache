<?php

namespace WordPress\Cache\Memcached;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheItemPool implements CacheItemPoolInterface {
	protected $config;

	/**
	 * Object_Cache_Controller_Implementation_Interface constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
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
	 * Deletes all items in the pool.
	 *
	 * @return bool
	 *   True if the pool was successfully cleared. False if there was an error.
	 */
	public function clear() {
		// TODO: Implement clear() method.
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