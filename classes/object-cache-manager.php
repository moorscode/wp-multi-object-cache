<?php

class Object_Cache_Manager {
	/** @var array */
	protected static $group_aliases = array();
	/** @var  array */
	protected static $controllers = array();
	/** @var array */
	protected static $controller_groups = array();

	/** @var int Blog ID */
	protected static $blog_id;

	public static function initialize() {
		if ( ! defined( 'WP_CACHE_KEY_SALT' ) ) {
			define( 'WP_CACHE_KEY_SALT', '' );
		}

		// @todo extract configuration
		// read configuration
		$config = array(
			'controllers' => array(
				// Default/fallback controller.
				'Redis_Controller'          => array(
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
				'Memcache_Controller'       => array(
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
				// Use Non Persistent Controller.
				'Non_Persistent_Controller' => array(
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
		self::register_controllers( $config['controllers'] );
	}

	/**
	 * @param Object_Cache_Controller_Interface $controller
	 * @param string $group
	 */
	public static function assign_group( Object_Cache_Controller_Interface $controller, $group ) {
		self::$controller_groups[ $group ] = $controller;
	}

	/**
	 * @return Object_Cache_Controller_Implementation_Interface[] All the controllers
	 */
	public static function get_controllers() {
		return self::$controllers;
	}

	/**
	 * @param string $group
	 *
	 * @return Object_Cache_Controller_Interface
	 */
	public static function get_controller( $group = '' ) {
		$use_group = $group;
		if ( isset( self::$group_aliases[ $group ] ) ) {
			$use_group = self::$group_aliases[ $use_group ];
		}

		$controller = new Null_Controller( array() );
		if ( isset( self::$controller_groups[ $use_group ] ) ) {
			$controller = self::$controller_groups[ $use_group ];
		}

		// Create a new Key Controller with initial group name.
		return new Object_Cache_Key_Controller( $controller, $group );
	}

	/**
	 * Switch to a different blog_id.
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
	 * Registers the controllers for the groups they specified.
	 *
	 * @param array $controllers List of controllers to load.
	 *
	 * @throws Exception
	 */
	protected static function register_controllers( $controllers ) {
		// Register controllers.
		foreach ( $controllers as $controller => $data ) {
			if ( ! class_exists( $controller ) ) {
				// Throw exception.
				throw new Exception( 'Class ' . $controller . ' not found while loading Object Cache Controllers.' );
			}

			self::$controllers[ $controller ] = new $controller( $data['config'] );
			foreach ( $data['groups'] as $group ) {
				self::assign_group( self::$controllers[ $controller ], $group );
			}
		}
	}
}
