<?php
/**
 * PHPUnit bootstrap for unit tests (no WordPress install required).
 *
 * Brain Monkey mocks WordPress functions so business logic is tested in
 * isolation. Constants the plugin expects from WordPress are defined here.
 *
 * @package Mdev\Gb\Tests
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}

require_once dirname( __DIR__ ) . '/wp-content/plugins/mdev-gb/tests/stubs/wordpress-classes.php';
