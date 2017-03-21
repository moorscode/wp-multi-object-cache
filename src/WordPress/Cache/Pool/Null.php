<?php

namespace WordPress\Cache\Pool;

use WordPress\Cache\WPCacheItemPoolInterface;

class Null implements WPCacheItemPoolInterface {

	/**
	 * Object_Cache_Controller_Implementation_Interface constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config ) {
	}

	/**
	 * Adds data to the cache, if the cache key doesn't already exist.
	 *
	 * @param int|string $key The cache key to use for retrieval later.
	 * @param mixed $data The data to add to the cache.
	 * @param int $expire Optional. When the cache data should expire, in seconds.
	 *                           Default 0 (no expiration).
	 *
	 * @return bool False if cache key and group already exist, true on success.
	 */
	public function add( $key, $data, $args = null ) {
		return false;
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
		return false;
	}

	/**
	 * Removes the cache contents matching key and group.
	 *
	 * @param int|string $key What the contents in the cache are called.
	 *
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete( $key, $args = null ) {
		return true;
	}

	/**
	 * Removes all cache items.
	 *
	 * @return bool False on failure, true on success
	 */
	public function flush($args = null) {
		return true;
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
		$found = false;

		return null;
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
		return false;
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
		return false;
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
		return false;
	}
}
