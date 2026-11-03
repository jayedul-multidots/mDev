<?php
/**
 * Post gallery feature wiring.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

use WP_Query;

/**
 * Builds the post gallery services and attaches them to WordPress hooks.
 *
 * @since 0.2.0
 */
final class Feature {

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param string $plugin_dir Absolute plugin directory, without trailing slash.
	 */
	public function __construct( private readonly string $plugin_dir ) {
	}

	/**
	 * Register hooks.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		$summary    = new ResultSummary();
		$repository = new GalleryRepository(
			static fn ( array $args ): WP_Query => new WP_Query( $args ),
			new PostCardMapper()
		);
		$renderer   = new GalleryRenderer(
			$repository,
			$summary,
			$this->plugin_dir . '/views/post-gallery.php',
			static fn (): array => self::query_params()
		);
		$controller = new RestController( $repository, $summary );

		add_filter( 'register_block_type_args', [ $renderer, 'filter_block_type_args' ], 10, 2 );
		add_action( 'rest_api_init', [ $controller, 'register_routes' ] );
	}

	/**
	 * Current query parameters.
	 *
	 * Read-only public filtering (search/sort/page of published posts): no
	 * state changes, so no nonce. Values are sanitized by GalleryQuery.
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, mixed>
	 */
	public static function query_params(): array {
		/**
		 * Query string keys are always strings.
		 *
		 * @var array<string, mixed> $params
		 */
		$params = wp_unslash( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only public filters, sanitized in GalleryQuery.

		return $params;
	}
}
