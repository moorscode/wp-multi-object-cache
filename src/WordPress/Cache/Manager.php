<?php

namespace WordPress\Cache;

use WordPress\Cache\Pool\Null;

class Manager {
	/** @var array */
	protected static $group_aliases = array();
	/** @var  array */
	protected static $pools = array();
	/** @var array */
	protected static $pool_groups = array();
	/** @var int Blog ID */
	protected static $blog_id;

	/**
	 * Initializes the manager.
	 */
	public static function initialize() {
		if ( ! defined( 'WP_CACHE_KEY_SALT' ) ) {
			define( 'WP_CACHE_KEY_SALT', '' );
		}

		// @todo extract configuration
		// read configuration
		$config = array(
			'pools' => array(
				// Default/fallback controller.
				'\WordPress\Cache\Pool\Redis'         => array(
					'data'   => array(
						'config' => array(
							'ip'   => '127.0.0.1',
							'port' => '1112'
						)
					),
					'groups' => array(
						''
					)
				),
				// Use Memcached controller for transients.
				'\WordPress\Cache\Pool\Memcache'      => array(
					'data'   => array(
						'config' => array(
							'ip'   => '127.0.0.1',
							'port' => '1112'
						)
					),
					'groups' => array(
						'site-transient'
					),
				),
				// Use Non Persistent Pool.
				'\WordPress\Cache\Pool\NonPersistent' => array(
					'data'   => array(
						'config' => array()
					),
					'groups' => array(
						'non-persistent'
					),
				),
			)
		);

		self::switch_to_blog( get_current_blog_id() );
		self::register_pools( $config['pools'] );
	}

	/**
	 * @param WPCacheItemPoolInterface $pool
	 * @param string $group
	 */
	public static function assign_group( WPCacheItemPoolInterface $pool, $group ) {
		self::$pool_groups[ $group ] = $pool;
	}

	/**
	 * Gets all registered pools.
	 *
	 * @return WPCacheItemPoolInterface[]
	 */
	public static function get_pools() {
		return self::$pools;
	}

	/**
	 * Gets the controller for a group.
	 *
	 * @param string $group Group to get controller for.
	 *
	 * @return WPCacheItemKeyContoller
	 */
	public static function get_controller( $group = '' ) {
		$use_group = $group;
		if ( isset( self::$group_aliases[ $group ] ) ) {
			$use_group = self::$group_aliases[ $use_group ];
		}

		$pool = new Null( array() );
		if ( isset( self::$pool_groups[ $use_group ] ) ) {
			$pool = self::$pool_groups[ $use_group ];
		}

		// Create a new Key Pool with initial group name.
		return new WPCacheItemKeyContoller( $pool, $group );
	}

	/**
	 * Switches to a specific blog_id.
	 *
	 * @param int $blog_id Blog to switch to.
	 */
	public static function switch_to_blog( $blog_id ) {
		self::$blog_id = $blog_id;
	}

	/**
	 * Adds an alias to a group, so the same controller will be used.
	 *
	 * @param string $group Group to add an alias for.
	 * @param string $alias Alias of the group.
	 */
	public static function add_group_alias( $group, $alias ) {
		/**
		 * @todo decide
		 * Use a filter instead?
		 * + Group Alias manager?
		 */
		self::$group_aliases[ $group ] = $alias;
	}

	/**
	 * Determines if we are in a multi-site environment.
	 *
	 * @return bool
	 */
	private static function is_multisite() {
		static $multisite;

		if ( null === $multisite ) {
			$multisite = false;
			if ( function_exists( 'is_multisite' ) ) {
				$multisite = is_multisite();
			}
		}

		return $multisite;
	}

	/**
	 * Gets the format the key should be following
	 *
	 * @return string
	 */
	public static function get_key_format() {
		global $table_prefix;

		// Allow for multiple sites to use the same Object Cache.
		$key_format = $table_prefix . ':%s';

		if ( self::is_multisite() ) {
			$key_format = self::$blog_id . ':%s';
		}

		return WP_CACHE_KEY_SALT . $key_format;
	}

	/**
	 * Registers the pools for the groups they specified.
	 *
	 * @param array $pools List of pools to load.
	 *
	 * @throws \Exception
	 */
	protected static function register_pools( $pools ) {
		// Register pools.
		foreach ( $pools as $pool => $data ) {
			if ( ! class_exists( $pool ) ) {
				// Throw exception.
				throw new \Exception( 'Class ' . $pool . ' not found while loading Object Cache pools.' );
			}

			self::$pools[ $pool ] = new $pool( $data['config'] );
			foreach ( $data['groups'] as $group ) {
				self::assign_group( self::$pools[ $pool ], $group );
			}
		}
	}
}
