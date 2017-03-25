<?php

namespace WordPress\Cache;

class KeyFormat {
	public function get() {
		global $table_prefix;

		// Allow for multiple sites to use the same Object Cache.
		$key_format = $table_prefix . ':%s';

		if ( $this->is_multisite() ) {
			$key_format = Manager::get_blog_id() . ':%s';
		}

		return WP_CACHE_KEY_SALT . $key_format;
	}

	/**
	 * Determines if we are in a multi-site environment.
	 *
	 * @return bool
	 */
	private function is_multisite() {
		static $multisite;

		if ( null === $multisite ) {
			$multisite = false;
			if ( function_exists( 'is_multisite' ) ) {
				$multisite = is_multisite();
			}
		}

		return $multisite;
	}
}