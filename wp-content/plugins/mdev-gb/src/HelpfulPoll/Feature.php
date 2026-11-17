<?php
/**
 * Helpful poll feature wiring.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

/**
 * Registers the vote count meta and the vote endpoint. The block itself is
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

		add_action( 'init', [ $counts, 'register_meta' ] );
		add_action( 'rest_api_init', [ $controller, 'register_routes' ] );
	}
}
