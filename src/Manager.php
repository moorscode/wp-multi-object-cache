<?php

namespace MultiObjectCache\Cache;

use Psr\Cache\CacheItemPoolInterface;

class Manager {
	/** @var PoolManager Pool Manager */
	protected static $pool_manager;

	/** @var CurrentBlogManager Blog Manager */
	protected static $blog_manager;

	/** @var GroupManager Group Manager */
	protected static $group_manager;

	/** @var KeyFormat $key_format */
	protected static $key_format;

	/** @var PoolGroupConnector Pool Group Connector */
	protected static $pool_group_connector;

	/**
	 * Initializes the manager.
	 *
	 * @throws \Exception
	 */
	public static function initialize() {
		if ( ! defined( 'WP_CACHE_KEY_SALT' ) ) {
			define( 'WP_CACHE_KEY_SALT', '' );
		}

		self::$group_manager = new GroupManager();
		self::$pool_group_connector = new PoolGroupConnector( self::$group_manager );

		self::$pool_manager = new PoolManager( self::$pool_group_connector, new PoolFactory() );
		self::$pool_manager->initialize();

		self::$blog_manager = new CurrentBlogManager( \get_current_blog_id() );

		self::$key_format = new KeyFormat();
	}

	/**
	 * @param CacheItemPoolInterface $pool
	 * @param string                 $group
	 */
	public static function assign_group( CacheItemPoolInterface $pool, $group ) {
		self::$pool_group_connector->add( $pool, $group );
	}

	/**
	 * Gets all registered pools.
	 *
	 * @return CacheItemPoolInterface[]
	 */
	public static function get_pools() {
		return self::$pool_manager->get_pools();
	}

	/**
	 * Gets the controller for a group.
	 *
	 * @param string $group Group to get controller for.
	 *
	 * @return CacheInterface
	 */
	public static function get_pool( $group = '' ) {
		return self::$pool_manager->get( $group );
	}

	/**
	 * Switches to a specific blog_id.
	 *
	 * @param int $blog_id Blog to switch to.
	 */
	public static function switch_to_blog( $blog_id ) {
		self::$blog_manager->switch_to_blog( $blog_id );
	}

	/**
	 * @return int
	 */
	public static function get_blog_id() {
		return self::$blog_manager->get_blog_id();
	}

	/**
	 * Adds an alias to a group, so the same controller will be used.
	 *
	 * @param string $group Group to add an alias for.
	 * @param string $alias Alias of the group.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function add_group_alias( $group, $alias ) {
		self::$group_manager->add_alias( $group, $alias );
	}

	/**
	 * Gets the format the key should be following
	 *
	 * @return string
	 */
	public static function get_key_format() {
		return self::$key_format->get();
	}
}
