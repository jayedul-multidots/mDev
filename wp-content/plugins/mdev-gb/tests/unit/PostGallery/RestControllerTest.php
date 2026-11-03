<?php
/**
 * Covers Mdev\Gb\PostGallery\RestController.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\GalleryRepository;
use Mdev\Gb\PostGallery\PostCardMapper;
use Mdev\Gb\PostGallery\RestController;
use Mdev\Gb\PostGallery\ResultSummary;
use WP_Query;
use WP_REST_Request;

/**
 * Route registration and request handling.
 * Techniques: a Mockery mock of WP_REST_Request, a Brain Monkey expectation
 * with an argument matcher on register_rest_route, and a fake query factory.
 */
final class RestControllerTest extends GalleryTestCase {

	/**
	 * Last WP_Query arguments.
	 *
	 * @var array<string, mixed>
	 */
	private array $last_args = [];

	/**
	 * Controller over a real repository with a fake query and an empty cache.
	 *
	 * @return RestController
	 */
	private function controller(): RestController {
		$this->stub_card_functions();
		Functions\stubs(
			[
				'wp_cache_get'                => false,
				'wp_cache_set'                => true,
				'wp_cache_get_last_changed'   => '1',
				'update_post_thumbnail_cache' => null,
			]
		);

		return new RestController(
			new GalleryRepository(
				function ( array $args ): WP_Query {
					$this->last_args      = $args;
					$query                = new WP_Query( $args );
					$query->posts         = [ $this->post( 2 ) ];
					$query->found_posts   = 5;
					$query->max_num_pages = 3;
					return $query;
				},
				new PostCardMapper()
			),
			new ResultSummary()
		);
	}

	/**
	 * Public GET route with the argument schema.
	 */
	public function test_registers_route(): void {
		$controller = $this->controller();

		Functions\expect( 'register_rest_route' )
			->once()
			->with(
				'mdev-gb/v1',
				'/post-gallery',
				\Mockery::on(
					static fn ( array $route ): bool => 'GET' === $route['methods']
						&& [ $controller, 'permissions_check' ] === $route['permission_callback']
						&& [ 'search', 'sort', 'default_sort', 'page', 'per_page', 'post_type', 'categories', 'image_size' ] === array_keys( $route['args'] )
				)
			);

		$controller->register_routes();
	}

	/**
	 * Hostile parameters are normalized even if schema validation were
	 * bypassed; the response carries data, summary and total headers.
	 */
	public function test_get_items_normalizes_hostile_input(): void {
		$params  = [
			'search'    => '<b>river</b>',
			'sort'      => 'rand',
			'page'      => -1,
			'per_page'  => 9999,
			'post_type' => 'attachment',
		];
		$request = \Mockery::mock( WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->andReturnUsing( static fn ( string $key ): mixed => $params[ $key ] ?? null );

		$response = $this->controller()->get_items( $request );

		self::assertSame( 'river', $this->last_args['s'] );
		self::assertSame( 'date', $this->last_args['orderby'] );
		self::assertSame( 1, $this->last_args['paged'] );
		self::assertSame( 24, $this->last_args['posts_per_page'] );
		self::assertSame( 'post', $this->last_args['post_type'] );
		self::assertSame( '5 posts found for “river”.', $response->get_data()['summary'] );
		self::assertSame( '5', $response->get_headers()['X-WP-Total'] );
	}
}
