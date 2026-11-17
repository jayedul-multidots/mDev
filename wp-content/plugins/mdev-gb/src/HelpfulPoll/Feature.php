<?php
/**
 * Helpful poll feature wiring.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

use WP_Block_Type_Registry;

/**
 * Registers the vote count meta, the vote endpoint, the admin list column
 * and the post type restriction in the editor. The block itself is
 * registered from the build manifest and renders through render.php.
 *
 * @since 0.3.0
 */
final class Feature {

	/**
	 * Register hooks.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public function register(): void {
		$counts     = new VoteCounts();
		$controller = new RestController( $counts, new VotablePolicy() );
		$columns    = new AdminColumns( $counts );

		add_action( 'init', [ $counts, 'register_meta' ] );
		add_action( 'rest_api_init', [ $controller, 'register_routes' ] );
		add_action( 'current_screen', [ new EditorAvailability( WP_Block_Type_Registry::get_instance() ), 'restrict' ] );

		foreach ( VotablePolicy::POST_TYPES as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", [ $columns, 'add_column' ] );
			add_action( "manage_{$post_type}_posts_custom_column", [ $columns, 'render_column' ], 10, 2 );
		}
	}
}
