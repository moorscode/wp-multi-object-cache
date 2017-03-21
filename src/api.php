<?php

/**
 * Object Cache API
 *
 * @subpackage Cache
 */

/**
 * Sets up Object Cache Global and assigns it.
 */
function wp_cache_init() {
	Object_Cache_Manager::initialize();
}

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @param int|string $key The cache key to use for retrieval later.
 * @param mixed $data The data to add to the cache.
 * @param string $group Optional. The group to add the cache to. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int $expire Optional. When the cache data should expire, in seconds.
 *                           Default 0 (no expiration).
 *
 * @return bool False if cache key and group already exist, true on success.
 */
function wp_cache_add( $key, $data, $group = '', $expire = 0 ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	$args = array(
		'expire' => (int) $expire
	);

	return $object_cache->add( $key, $data, $args );
}

/**
 * Closes the cache.
 *
 * This function has ceased to do anything since WordPress 2.5. The
 * functionality was removed along with the rest of the persistent cache.
 *
 * This does not mean that plugins can't implement this function when they need
 * to make sure that the cache is cleaned up after WordPress no longer needs it.
 *
 * @return true Always returns true.
 */
function wp_cache_close() {
	return true;
}

/**
 * Decrements numeric cache item's value.
 *
 * @param int|string $key The cache key to decrement.
 * @param int $offset Optional. The amount by which to decrement the item's value. Default 1.
 * @param string $group Optional. The group the key is in. Default empty.
 *
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_decr( $key, $offset = 1, $group = '' ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	return $object_cache->decrease( $key, $offset );
}

/**
 * Removes the cache contents matching key and group.
 *
 * @param int|string $key What the contents in the cache are called.
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 *
 * @return bool True on successful removal, false on failure.
 */
function wp_cache_delete( $key, $group = '' ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	return $object_cache->delete( $key );
}

/**
 * Removes all cache items.
 *
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 *
 * @return bool False on failure, true on success
 */
function wp_cache_flush( $group = null ) {
	// No group supplied, flush everything.
	if ( null === $group ) {
		return wp_cache_flush_all();
	}

	// Flush specific group.
	$object_cache = Object_Cache_Manager::get_controller( $group );

	return $object_cache->flush();
}

/**
 * Removes all cache items from all controllers.
 *
 * @return bool False on failure, true on success
 */
function wp_cache_flush_all() {
	$success = true;

	$controllers = Object_Cache_Manager::get_controllers();
	foreach ( $controllers as $controller ) {
		// @todo track status per controller for request?
		$success = $controller->flush() && $success;
	}

	return $success;
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @param int|string $key The key under which the cache contents are stored.
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool $force Optional. Whether to force an update of the local cache from the persistent
 *                            cache. Default false.
 * @param bool $found Optional. Whether the key was found in the cache. Disambiguates a return of false,
 *                            a storable value. Passed by reference. Default null.
 *
 * @return bool|mixed False on failure to retrieve contents or the cache
 *                      contents on success
 */
function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	return $object_cache->get( $key, $force, $found );
}

/**
 * Increment numeric cache item's value
 *
 * @param int|string $key The key for the cache contents that should be incremented.
 * @param int $offset Optional. The amount by which to increment the item's value. Default 1.
 * @param string $group Optional. The group the key is in. Default empty.
 *
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_incr( $key, $offset = 1, $group = '' ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	return $object_cache->increase( $key, $offset );
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @param int|string $key The key for the cache data that should be replaced.
 * @param mixed $data The new data to store in the cache.
 * @param string $group Optional. The group for the cache data that should be replaced.
 *                           Default empty.
 * @param int $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 *
 * @return bool False if original value does not exist, true if contents were replaced
 */
function wp_cache_replace( $key, $data, $group = '', $expire = 0 ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	$args = array(
		'expire' => (int) $expire
	);

	return $object_cache->replace( $key, $data, $args );
}

/**
 * Saves the data to the cache.
 *
 * Differs from wp_cache_add() and wp_cache_replace() in that it will always write data.
 *
 * @param int|string $key The cache key to use for retrieval later.
 * @param mixed $data The contents to store in the cache.
 * @param string $group Optional. Where to group the cache contents. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 *
 * @return bool False on failure, true on success
 */
function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
	$object_cache = Object_Cache_Manager::get_controller( $group );

	$args = array(
		'expire' => (int) $expire
	);

	return $object_cache->set( $key, $data, $args );
}

/**
 * Switches the internal blog ID.
 *
 * This changes the blog id used to create keys in blog specific groups.
 *
 * @param int $blog_id Site ID.
 */
function wp_cache_switch_to_blog( $blog_id ) {
	Object_Cache_Manager::switch_to_blog( $blog_id );
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @param string|array $groups A group or an array of groups to add.
 */
function wp_cache_add_global_groups( $groups ) {
	$groups = (array) $groups;
	foreach ( $groups as $group ) {
		Object_Cache_Manager::add_group_alias( '', $group );
	}
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @param string|array $groups A group or an array of groups to add.
 */
function wp_cache_add_non_persistent_groups( $groups ) {
	// Default cache doesn't persist so nothing to do here.
	$groups = (array) $groups;
	foreach ( $groups as $group ) {
		Object_Cache_Manager::add_group_alias( 'non-persistent', $group );
	}
}

/**
 * Reset internal cache keys and structures.
 *
 * If the cache back end uses global blog or site IDs as part of its cache keys,
 * this function instructs the back end to reset those keys and perform any cleanup
 * since blog or site IDs have changed since cache init.
 *
 * This function is deprecated. Use wp_cache_switch_to_blog() instead of this
 * function when preparing the cache for a blog switch. For clearing the cache
 * during unit tests, consider using wp_cache_init(). wp_cache_init() is not
 * recommended outside of unit tests as the performance penalty for using it is
 * high.
 *
 * @since 2.6.0
 * @deprecated 3.5.0 WP_Object_Cache::reset()
 * @see WP_Object_Cache::reset()
 */
function wp_cache_reset() {
	_deprecated_function( __FUNCTION__, '3.5.0' );
}
