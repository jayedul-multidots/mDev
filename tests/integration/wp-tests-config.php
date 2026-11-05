<?php
/**
 * WordPress core test suite configuration (read by wp-phpunit through the
 * WP_PHPUNIT__TESTS_CONFIG environment variable, see bootstrap.php).
 *
 * Runs inside the `wordpress` dev container: the WordPress files are the
 * container's, the database credentials come from its WORDPRESS_DB_*
 * environment. The database is a SEPARATE one (`<DB_NAME>_test`, created by
 * `make test-integration`): the suite drops and reinstalls every `wptests_`
 * table on each run, so it must never point at the site's database.
 *
 * @package Mdev\Gb\Tests
 */

$mdev_site_db = (string) getenv( 'WORDPRESS_DB_NAME' );
$mdev_test_db = (string) ( getenv( 'WP_TESTS_DB_NAME' ) ?: $mdev_site_db . '_test' );

if ( '' === $mdev_site_db || $mdev_test_db === $mdev_site_db ) {
	fwrite( STDERR, "Integration tests: refusing to run without a separate test database (WORDPRESS_DB_NAME is empty or equals the test database).\n" );
	exit( 1 );
}

define( 'ABSPATH', rtrim( (string) ( getenv( 'WP_TESTS_ABSPATH' ) ?: '/var/www/html' ), '/' ) . '/' );

define( 'DB_NAME', $mdev_test_db );
define( 'DB_USER', (string) getenv( 'WORDPRESS_DB_USER' ) );
define( 'DB_PASSWORD', (string) getenv( 'WORDPRESS_DB_PASSWORD' ) );
define( 'DB_HOST', (string) getenv( 'WORDPRESS_DB_HOST' ) );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );

// The site's mu-plugins (VIP platform code) must not load into the clean
// test install: point WordPress at a folder that does not exist.
define( 'WPMU_PLUGIN_DIR', __DIR__ . '/no-mu-plugins' );

unset( $mdev_site_db, $mdev_test_db );
