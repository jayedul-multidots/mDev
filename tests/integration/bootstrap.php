<?php
/**
 * PHPUnit bootstrap for integration tests: a real WordPress (core test suite,
 * via wp-phpunit) on a separate test database, with the mdev-gb plugin
 * loaded. Run with `make test-integration`.
 *
 * Unit tests (tests/bootstrap.php) mock WordPress instead; these check that
 * the code works with the real thing (WP_Query, REST server, Interactivity
 * API rendering, ...).
 *
 * @package Mdev\Gb\Tests
 */

$mdev_vendor = __DIR__ . '/vendor';

if ( ! is_readable( $mdev_vendor . '/autoload.php' ) ) {
	fwrite( STDERR, "Integration tests: dependencies missing, run `make test-integration` (it installs them).\n" );
	exit( 1 );
}

require_once $mdev_vendor . '/autoload.php';

putenv( 'WP_PHPUNIT__TESTS_CONFIG=' . __DIR__ . '/wp-tests-config.php' );

$mdev_tests_dir = (string) ( getenv( 'WP_TESTS_DIR' ) ?: $mdev_vendor . '/wp-phpunit/wp-phpunit' );

require_once $mdev_tests_dir . '/includes/functions.php';

// Load the plugin like WordPress would, before the theme and `init`.
tests_add_filter(
	'muplugins_loaded',
	static function (): void {
		require WP_PLUGIN_DIR . '/mdev-gb/mdev-gb.php';
	}
);

require $mdev_tests_dir . '/includes/bootstrap.php';
