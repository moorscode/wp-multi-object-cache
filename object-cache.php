<?php
/**
 * Bootstrap for Object Cache implementations
 *
 * Place this file at /wp-content/object-cache.php
 * Either by coping, symlinking or moving the file.
 */

// Make sure the repository checkout location matches this location:
$base_path = __DIR__ . '/mu-plugins/multi-object-cache';

require_once $base_path . '/vendor/autoload.php';
require_once $base_path . '/api.php';

unset($base_path);
