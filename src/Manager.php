<?php

namespace WPMultiObjectCache;

use Psr\Cache\CacheItemPoolInterface;

class Manager {
	/** @var PoolManager Pool Manager */
	protected static $poolManager;

	/** @var CurrentBlogManager Blog Manager */
	protected static $blogManager;

	/** @var GroupManager Group Manager */
	protected static $groupManager;

	/** @var KeyFormat $keyFormat */
	protected static $keyFormat;

	/** @var PoolGroupConnector Pool Group Connector */
	protected static $poolGroupConnector;

	/**
	 * Initializes the manager.
	 *
	 * @throws \Exception
	 */
	public static function initialize() {
		if ( ! defined( 'WP_CACHE_KEY_SALT' ) ) {
			define( 'WP_CACHE_KEY_SALT', '' );
		}

		$adminNotifier = new AdminNotifier();
		$adminNotifier->add_hooks();

		$factory = new PoolFactory( $adminNotifier );

		self::$groupManager       = new GroupManager();
		self::$poolGroupConnector = new PoolGroupConnector( self::$groupManager, $factory );

		self::$poolManager = new PoolManager( self::$poolGroupConnector, $factory );
		self::$poolManager->initialize();

		self::$blogManager = new CurrentBlogManager( \get_current_blog_id() );

		self::$keyFormat = new KeyFormat( self::$blogManager );
	}

	/**
	 * Assigns a group to the connector.
	 *
	 * @param CacheItemPoolInterface $pool
	 * @param string                 $group
	 */
	public static function assignGroup( CacheItemPoolInterface $pool, $group ) {
		self::$poolGroupConnector->add( $pool, $group );
	}

	/**
	 * Gets all registered pools.
	 *
	 * @return CacheItemPoolInterface[]
	 */
	public static function getPools() {
		return self::$poolManager->getPools();
	}

	/**
	 * Gets the controller for a group.
	 *
	 * @param string $group Group to get controller for.
	 *
	 * @return CacheInterface
	 */
	public static function getPool( $group = '' ) {
		return self::$poolManager->get( $group );
	}

	/**
	 * Switches to a specific blog_id.
	 *
	 * @param int $blogID Blog to switch to.
	 */
	public static function switchToBlog( $blogID ) {
		self::$blogManager->switchToBlog( $blogID );
	}

	/**
	 * Adds an alias to a group, so the same controller will be used.
	 *
	 * @param string $group Group to add an alias for.
	 * @param string $alias Alias of the group.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function addGroupAlias( $group, $alias ) {
		self::$groupManager->addAlias( $group, $alias );
	}

	/**
	 * Gets the format the key should be following
	 *
	 * @return string
	 */
	public static function getKeyFormat() {
		return self::$keyFormat->get();
	}

	/**
	 * Sets a custom error handler
	 */
	public static function convertWarningToException() {
		set_error_handler( [ __CLASS__, 'warningToExceptionHandler' ] );
	}

	/**
	 * Restores the default error handler
	 */
	public static function restoreErrorHandling() {
		restore_error_handler();
	}

	/**
	 * Converts a warning to a exception
	 *
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 *
	 * @return bool
	 * @throws \ErrorException
	 */
	public static function warningToExceptionHandler( $errno, $errstr, $errfile, $errline ) {
		// error was suppressed with the @-operator
		if ( 0 === error_reporting() ) {
			return false;
		}

		if ( $errno === E_WARNING ) {
			throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
		}

		return false;
	}
}
