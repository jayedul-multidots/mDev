<?php
/**
 * Environment-driven WordPress configuration for the mdev image.
 *
 * Required from the generated wp-config.php through WORDPRESS_CONFIG_EXTRA.
 * Every value comes from the container environment (populated from `.env`);
 * no secrets live in this file. `getenv_docker()` is defined by the upstream
 * wp-config-docker.php template before this file is loaded.
 *
 * @package Mdev
 */

define( 'WP_ENVIRONMENT_TYPE', getenv_docker( 'WP_ENVIRONMENT_TYPE', 'production' ) );

$mdev_home = getenv_docker( 'WP_HOME', '' );
if ( '' !== $mdev_home ) {
	define( 'WP_HOME', rtrim( $mdev_home, '/' ) );
	define( 'WP_SITEURL', rtrim( $mdev_home, '/' ) );
}
unset( $mdev_home );

// Code ships in the image; never let WordPress edit or install it at runtime.
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', filter_var( getenv_docker( 'WP_DISALLOW_FILE_MODS', 'true' ), FILTER_VALIDATE_BOOLEAN ) );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_POST_REVISIONS', 10 );

// TLS terminated upstream (e.g. Cloudflare -> Traefik `http` entrypoint).
if ( filter_var( getenv_docker( 'WP_FORCE_SSL_PROXY', 'false' ), FILTER_VALIDATE_BOOLEAN ) ) {
	$_SERVER['HTTPS'] = 'on';
}

if ( 'local' === WP_ENVIRONMENT_TYPE || 'development' === WP_ENVIRONMENT_TYPE ) {
	define( 'WP_DEBUG_LOG', '/tmp/wp-debug.log' );
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'SCRIPT_DEBUG', true );
} else {
	define( 'WP_DEBUG_DISPLAY', false );
}
