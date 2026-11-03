<?php
/**
 * Plugin Name:       mdev Gutenberg Blocks
 * Description:       Custom block editor blocks for the mdev site.
 * Version:           0.1.0
 * Requires at least: 6.8
 * Requires PHP:      8.3
 * Author:            mdev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mdev-gb
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.1.0';

/**
 * Show an admin notice when the Composer autoloader has not been generated.
 *
 * @since 0.1.0
 *
 * @return void
 */
function missing_autoloader_notice(): void {
	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( 'mdev Gutenberg Blocks: the Composer autoloader is missing. Run `composer install` in the repository root.', 'mdev-gb' )
	);
}

$mdev_gb_autoload = __DIR__ . '/vendor/autoload.php';

if ( ! is_readable( $mdev_gb_autoload ) ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\\missing_autoloader_notice' );
	return;
}

require_once $mdev_gb_autoload;

( new Plugin( __DIR__ ) )->boot();
