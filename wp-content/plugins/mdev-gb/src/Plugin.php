<?php
/**
 * Composition root for the mdev-gb plugin.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb;

use Mdev\Gb\Blocks\BlockCategory;
use Mdev\Gb\Blocks\BlockRegistrar;

/**
 * Instantiates the plugin features and wires them to WordPress hooks.
 *
 * @since 0.1.0
 */
final class Plugin {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_dir Absolute path to the plugin directory, without trailing slash.
	 */
	public function __construct( private readonly string $plugin_dir ) {
	}

	/**
	 * Register all hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function boot(): void {
		$registrar = new BlockRegistrar( $this->plugin_dir . '/build' );
		$category  = new BlockCategory();

		add_action( 'init', [ $registrar, 'register' ] );
		add_filter( 'block_categories_all', [ $category, 'register' ] );
	}
}
