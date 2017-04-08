<?php

namespace WPMultiObjectCache;

class KeyFormat {
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
			$key_format = Manager::getBlogID() . '.%s';
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
