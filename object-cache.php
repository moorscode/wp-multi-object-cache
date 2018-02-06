<?php
/**
 * Bootstrap for Object Cache implementations
 *
 * Place this file at /wp-content/object-cache.php
 * Either by coping, symlinking or moving the file.
 */

if (PHP_VERSION_ID < 50400) {
    printf('wp-multi-object-cache requires at least PHP 5.4, you are running %s.', PHP_VERSION);
    exit;
}

// Make sure the repository checkout location matches this location:
$base_path = __DIR__ . '/mu-plugins/wp-multi-object-cache';

if (! is_dir($base_path)) {
    printf(
        'Please modify the $base_path variable in %s to match the folder ' .
        'in which you have checked out the wp-multi-object-cache repository.',
        __FILE__
    );
    exit;
}

if (is_dir($base_path) && ! is_dir($base_path . '/vendor/')) {
    printf('Please run `composer install` to generate the %s/vendor/ folder.', $base_path);
    echo PHP_EOL;
    printf('If you don\'t have composer installed, please see https://getcomposer.org for installation instructions.');
    exit;
}

require_once $base_path . '/vendor/autoload.php';
require_once $base_path . '/api.php';

unset($base_path);
