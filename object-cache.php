<?php
/**
 * Bootstrap for Object Cache implementations
 */

if ( ! defined( 'OBJECT_CACHE_PATH' ) ) {
	define( 'OBJECT_CACHE_PATH', __DIR__ );
}

require_once 'object-cache/autoloader.php';
require_once 'object-cache/api.php';
