<?php

spl_autoload_register( function ( $class ) {
	$namespaces = [
		'\\Psr\\Cache\\',
		'\\WordPress\\Cache\\'
	];

	foreach ( $namespaces as $namespace ) {
		if ( 0 !== strpos( $class, $namespace ) ) {
			continue;
		}

		$filename = __DIR__ . $class . '.php';

		if ( is_file( $filename ) ) {
			require_once $filename;

			return true;
		}
	}

	return false;
} );
