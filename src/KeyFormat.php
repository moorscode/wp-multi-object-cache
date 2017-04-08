<?php

namespace WPMultiObjectCache;

class KeyFormat {

	/** @var CurrentBlogManager Blog Manager */
	private $blogManager;

	/**
	 * KeyFormat constructor.
	 *
	 * @param CurrentBlogManager $blogManager
	 */
	public function __construct( CurrentBlogManager $blogManager ) {
		$this->blogManager = $blogManager;
	}

	/**
	 * Gets the cache key format to be used
	 *
	 * @return string
	 */
	public function get() {
		global $table_prefix;

		// Allow for multiple sites to use the same Object Cache.
		$key_format = $table_prefix . '%s';

		if ( $this->isMultiSite() ) {
			$key_format = $this->blogManager->getBlogID() . '.%s';
		}

		return WP_CACHE_KEY_SALT . $key_format;
	}

	/**
	 * Determines if we are in a multi-site environment.
	 *
	 * @return bool
	 */
	private function isMultiSite() {
		static $multiSite;

		if ( null === $multiSite ) {
			$multiSite = ( function_exists( 'is_multisite' ) && is_multisite() );
		}

		return $multiSite;
	}
}
