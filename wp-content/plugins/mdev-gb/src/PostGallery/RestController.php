<?php
/**
 * REST endpoint for the post gallery.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

use WP_REST_Request;
use WP_REST_Response;

/**
 * `GET /wp-json/mdev-gb/v1/post-gallery`: one page of gallery cards.
 *
 * Read-only and public: it only ever returns published, non-password-protected
 * posts of publicly viewable post types, i.e. what the site already shows to
 * anonymous visitors. Every parameter is validated by the schema below and
 * normalized again by GallerySettings / GalleryQuery.
 *
 * @since 0.2.0
 */
final class RestController {

	public const NAMESPACE = 'mdev-gb/v1';
	public const ROUTE     = '/post-gallery';

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param GalleryRepository $repository Cached queries.
	 * @param ResultSummary     $summary    Summary text.
	 */
	public function __construct(
		private readonly GalleryRepository $repository,
		private readonly ResultSummary $summary,
	) {
	}

	/**
	 * Register the route. Hooked to `rest_api_init`.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'permissions_check' ],
				'args'                => $this->get_args(),
			]
		);
	}

	/**
	 * Anyone may read: the endpoint exposes only publicly visible content (see
	 * class docblock), the same as the site's own archives and search.
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public function permissions_check(): bool {
		return true;
	}

	/**
	 * Return one page of cards.
	 *
	 * @since 0.2.0
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 *
	 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
	 */
	public function get_items( WP_REST_Request $request ): WP_REST_Response {
		$settings = GallerySettings::from_attributes(
			[
				'postType'    => $request->get_param( 'post_type' ),
				'perPage'     => $request->get_param( 'per_page' ),
				'categories'  => $request->get_param( 'categories' ),
				'imageSize'   => $request->get_param( 'image_size' ),
				'defaultSort' => $request->get_param( 'default_sort' ),
			]
		);
		$query    = GalleryQuery::from_input(
			$settings,
			[
				'search' => $request->get_param( 'search' ),
				'sort'   => $request->get_param( 'sort' ),
				'page'   => $request->get_param( 'page' ),
			]
		);
		$result   = $this->repository->find( $query );

		$response = new WP_REST_Response(
			$result->to_array() + [ 'summary' => $this->summary->describe( $result, $query->search ) ]
		);
		$response->header( 'X-WP-Total', (string) $result->total );
		$response->header( 'X-WP-TotalPages', (string) $result->total_pages );

		return $response;
	}

	/**
	 * Argument schema: types, bounds and allowlists enforced by WordPress
	 * before the callback runs (invalid input gets a 400 response).
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_args(): array {
		return [
			'search'       => [
				'type'              => 'string',
				'default'           => '',
				'maxLength'         => GalleryQuery::MAX_SEARCH_LENGTH,
				'sanitize_callback' => 'sanitize_text_field',
			],
			'sort'         => [
				'type'    => 'string',
				'enum'    => SortOption::values(),
				'default' => SortOption::Newest->value,
			],
			'default_sort' => [
				'type'    => 'string',
				'enum'    => SortOption::values(),
				'default' => SortOption::Newest->value,
			],
			'page'         => [
				'type'    => 'integer',
				'minimum' => 1,
				'maximum' => GalleryQuery::MAX_PAGE,
				'default' => 1,
			],
			'per_page'     => [
				'type'    => 'integer',
				'minimum' => 1,
				'maximum' => GallerySettings::MAX_PER_PAGE,
				'default' => GallerySettings::DEFAULT_PER_PAGE,
			],
			'post_type'    => [
				'type'              => 'string',
				'default'           => GallerySettings::DEFAULT_POST_TYPE,
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => [ $this, 'validate_post_type' ],
			],
			'categories'   => [
				'type'     => 'array',
				'items'    => [
					'type'    => 'integer',
					'minimum' => 1,
				],
				'maxItems' => GallerySettings::MAX_CATEGORIES,
				'default'  => [],
			],
			'image_size'   => [
				'type'              => 'string',
				'default'           => GallerySettings::DEFAULT_IMAGE_SIZE,
				'sanitize_callback' => 'sanitize_key',
			],
		];
	}

	/**
	 * Only public, viewable post types may be queried.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $value Raw parameter.
	 * @return bool
	 */
	public function validate_post_type( mixed $value ): bool {
		return GallerySettings::is_allowed_post_type( $value );
	}
}
