<?php
/**
 * Covers Mdev\Gb\PostGallery\RestController (with GallerySettings,
 * GalleryQuery and GalleryRepository) against a real WordPress: REST server,
 * argument validation, WP_Query and the database.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Integration\PostGallery;

use WP_REST_Request;
use WP_REST_Response;
use WP_UnitTestCase;

/**
 * `GET /wp-json/mdev-gb/v1/post-gallery` with posts created by factories.
 */
final class RestEndpointTest extends WP_UnitTestCase {

	private const ROUTE = '/mdev-gb/v1/post-gallery';

	private const VIDEO_POST_TYPE = 'deep_pockets_videos';

	/**
	 * Fresh REST server per test, so route defaults (e.g. the default post
	 * type) are computed from the post types registered in that test.
	 */
	public function set_up(): void {
		parent::set_up();
		$GLOBALS['wp_rest_server'] = null;
	}

	/**
	 * Drop the REST server built for this test, and the video post type:
	 * outside core's own tests, WordPress does not reset post types between
	 * tests.
	 */
	public function tear_down(): void {
		if ( post_type_exists( self::VIDEO_POST_TYPE ) ) {
			unregister_post_type( self::VIDEO_POST_TYPE );
		}
		$GLOBALS['wp_rest_server'] = null;
		parent::tear_down();
	}

	/**
	 * Draft, private and password-protected posts are never listed.
	 */
	public function test_lists_only_published_unprotected_posts(): void {
		self::factory()->post->create( [ 'post_title' => 'Published' ] );
		self::factory()->post->create(
			[
				'post_title'  => 'Draft',
				'post_status' => 'draft',
			]
		);
		self::factory()->post->create(
			[
				'post_title'  => 'Private',
				'post_status' => 'private',
			]
		);
		self::factory()->post->create(
			[
				'post_title'    => 'Protected',
				'post_password' => 'secret',
			]
		);

		$response = $this->get( [ 'post_type' => 'post' ] );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [ 'Published' ], $this->titles( $response ) );
		$this->assertSame( '1', $response->get_headers()['X-WP-Total'] );
	}

	/**
	 * Search goes through WP_Query and only returns matching posts.
	 */
	public function test_search_returns_matching_posts(): void {
		self::factory()->post->create( [ 'post_title' => 'Building better habits' ] );
		self::factory()->post->create( [ 'post_title' => 'Gardening basics' ] );
		self::factory()->post->create( [ 'post_title' => 'Wealth building' ] );

		$response = $this->get(
			[
				'post_type' => 'post',
				'search'    => 'building',
			]
		);

		$this->assertEqualsCanonicalizing(
			[ 'Building better habits', 'Wealth building' ],
			$this->titles( $response )
		);
		$this->assertSame( '2 posts found for “building”.', $response->get_data()['summary'] );
	}

	/**
	 * Title sorts order the results in the database query.
	 *
	 * @dataProvider title_sorts
	 *
	 * @param string   $sort     Sort value.
	 * @param string[] $expected Titles in order.
	 */
	public function test_title_sorts( string $sort, array $expected ): void {
		foreach ( [ 'Banana', 'apple', 'Cherry' ] as $title ) {
			self::factory()->post->create( [ 'post_title' => $title ] );
		}

		$response = $this->get(
			[
				'post_type' => 'post',
				'sort'      => $sort,
			]
		);

		$this->assertSame( $expected, $this->titles( $response ) );
	}

	/**
	 * Title sort cases (case-insensitive, as the database compares them).
	 *
	 * @return array<string, array{string, string[]}>
	 */
	public function title_sorts(): array {
		return [
			'A to Z' => [ 'title-asc', [ 'apple', 'Banana', 'Cherry' ] ],
			'Z to A' => [ 'title-desc', [ 'Cherry', 'Banana', 'apple' ] ],
		];
	}

	/**
	 * Pages, totals and "has more" come from the real query.
	 */
	public function test_pagination(): void {
		self::factory()->post->create_many( 3 );

		$first  = $this->get(
			[
				'post_type' => 'post',
				'per_page'  => 2,
			]
		)->get_data();
		$second = $this->get(
			[
				'post_type' => 'post',
				'per_page'  => 2,
				'page'      => 2,
			]
		)->get_data();

		$this->assertCount( 2, $first['items'] );
		$this->assertSame( 3, $first['total'] );
		$this->assertSame( 2, $first['totalPages'] );
		$this->assertTrue( $first['hasMore'] );
		$this->assertCount( 1, $second['items'] );
		$this->assertFalse( $second['hasMore'] );
		$this->assertEmpty(
			array_intersect( array_column( $first['items'], 'id' ), array_column( $second['items'], 'id' ) ),
			'Pages must not overlap.'
		);
	}

	/**
	 * WordPress rejects invalid parameters before the callback runs.
	 *
	 * @dataProvider invalid_params
	 *
	 * @param array<string, mixed> $params Query parameters.
	 */
	public function test_invalid_params_are_rejected( array $params ): void {
		$response = $this->get( $params );

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	/**
	 * Invalid parameter cases.
	 *
	 * @return array<string, array{array<string, mixed>}>
	 */
	public function invalid_params(): array {
		return [
			'per_page above maximum'  => [ [ 'per_page' => 999 ] ],
			'page below minimum'      => [ [ 'page' => 0 ] ],
			'unknown sort'            => [ [ 'sort' => 'rand' ] ],
			'attachments'             => [ [ 'post_type' => 'attachment' ] ],
			'unregistered post type'  => [ [ 'post_type' => 'nope' ] ],
		];
	}

	/**
	 * With the video post type registered, it is the default.
	 */
	public function test_defaults_to_videos_when_registered(): void {
		register_post_type(
			self::VIDEO_POST_TYPE,
			[
				'public'       => true,
				'show_in_rest' => true,
			]
		);
		self::factory()->post->create( [ 'post_title' => 'A post' ] );
		self::factory()->post->create(
			[
				'post_title' => 'A video',
				'post_type'  => self::VIDEO_POST_TYPE,
			]
		);

		$this->assertSame( [ 'A video' ], $this->titles( $this->get( [] ) ) );
	}

	/**
	 * Without it (e.g. another theme active), posts are listed.
	 */
	public function test_defaults_to_posts_without_videos(): void {
		self::factory()->post->create( [ 'post_title' => 'A post' ] );

		$this->assertSame( [ 'A post' ], $this->titles( $this->get( [] ) ) );
	}

	/**
	 * Dispatch a GET request to the gallery route.
	 *
	 * @param array<string, mixed> $params Query parameters.
	 * @return WP_REST_Response
	 */
	private function get( array $params ): WP_REST_Response {
		$request = new WP_REST_Request( 'GET', self::ROUTE );
		$request->set_query_params( $params );

		return rest_do_request( $request );
	}

	/**
	 * Card titles of a response, in order.
	 *
	 * @param WP_REST_Response $response Response.
	 * @return string[]
	 */
	private function titles( WP_REST_Response $response ): array {
		return array_column( $response->get_data()['items'], 'title' );
	}
}
