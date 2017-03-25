<?php

namespace WordPress\Cache;

use Psr\Cache\CacheItemPoolInterface;

class PSRCacheAdapter implements CacheInterface {

	/** @var CacheItemPoolInterface Pool to use */
	protected $pool;

	/** @var string Group that was called for */
	protected $group;

	/**
	 * WPCachePSRAdapter constructor.
	 *
	 * @param CacheItemPoolInterface $pool
	 * @param string                 $group
	 */
	public function __construct( CacheItemPoolInterface $pool, $group ) {
		$this->pool  = $pool;
		$this->group = $group;
	}

	/**
	 * Builds the unique cache key for the contextual request.
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
	 * @param int|string $key    The cache key to use for retrieval later.
	 * @param mixed      $data   The data to add to the cache.
	 * @param int        $expire Optional. When the cache data should expire, in seconds.
	 *                           Default 0 (no expiration).
	 *
	 * @return bool False if cache key and group already exist, true on success.
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function add( $key, $data, $expire = null ) {
		if ( $this->pool->hasItem( $key ) ) {
			return false;
		}

		return $this->set( $key, $data, $expire );
	}

	/**
	 * Decrements numeric cache item's value.
	 *
	 * @param int|string $key    The cache key to decrement.
	 * @param int        $offset Optional. The amount by which to decrement the item's value. Default 1.
	 *
	 * @return false|int False on failure, the item's new value on success.
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function decrease( $key, $offset = 1 ) {
		if ( ! is_int( $offset ) ) {
			throw new \InvalidArgumentException( 'Offset should be an integer.' );
		}

		return $this->increase( $key, - $offset );
	}

	/**
	 * Removes the cache contents matching key and group.
	 *
	 * @param int|string $key What the contents in the cache are called.
	 *
	 * @return bool True on successful removal, false on failure.
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function delete( $key ) {
		return $this->pool->deleteItem( $key );
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
	 * @param int|string $key     The key under which the cache contents are stored.
	 * @param bool       $force   Optional. Whether to force an update of the local cache from the persistent
	 *                            cache. Default false.
	 * @param bool       $found   Optional. Whether the key was found in the cache. Disambiguates a return of false,
	 *                            a storable value. Passed by reference. Default null.
	 *
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 *                      contents on success
	 */
	public function get( $key, $force = false, &$found = null ) {
		$found = $this->pool->hasItem( $key );
		if ( $found ) {
			return $this->pool->getItem( $key )->get();
		}

		return false;
	}

	/**
	 * Increment numeric cache item's value
	 *
	 * @param int|string $key    The key for the cache contents that should be incremented.
	 * @param int        $offset Optional. The amount by which to increment the item's value. Default 1.
	 *
	 * @return false|int False on failure, the item's new value on success.
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function increase( $key, $offset = 1 ) {
		if ( ! is_int( $offset ) ) {
			throw new \InvalidArgumentException( 'Offset should be an integer.' );
		}

		if ( ! $this->pool->hasItem( $key ) ) {
			return false;
		}

		$item  = $this->pool->getItem( $key );
		$value = $item->get();

		if ( null === $value ) {
			$value = 0;
		}

		$value += $offset;

		$item->set( $value );

		return $item->get();
	}

	/**
	 * Replaces the contents of the cache with new data.
	 *
	 * @param int|string $key    The key for the cache data that should be replaced.
	 * @param mixed      $data   The new data to store in the cache.
	 * @param int        $expire Optional. When to expire the cache contents, in seconds.
	 *                           Default 0 (no expiration).
	 *
	 * @return bool False if original value does not exist, true if contents were replaced.
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function replace( $key, $data, $expire = null ) {
		if ( ! $this->pool->hasItem( $key ) ) {
			return false;
		}

		return $this->set( $key, $data, $expire );
	}

	/**
	 * Saves the data to the cache.
	 *
	 * Differs from wp_cache_add() and wp_cache_replace() in that it will always write data.
	 *
	 * @param int|string $key    The cache key to use for retrieval later.
	 * @param mixed      $data   The contents to store in the cache.
	 *                           to be used across groups. Default empty.
	 * @param int        $expire Optional. When to expire the cache contents, in seconds.
	 *                           Default 0 (no expiration).
	 *
	 * @return bool False on failure, true on success
	 *
	 * @throws \Psr\Cache\InvalidArgumentException
	 */
	public function set( $key, $data, $expire = 0 ) {
		$item = $this->pool->getItem( $key );
		$item->set( $data );

		if ( $expire ) {
			if ( $expire > YEAR_IN_SECONDS ) {
				$item->expiresAt( $expire );
			} else {
				$item->expiresAfter( $expire );
			}
		}

		return $this->pool->save( $item );
	}
}
