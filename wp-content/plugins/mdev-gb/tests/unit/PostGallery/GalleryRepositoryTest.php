<?php
/**
 * Covers Mdev\Gb\PostGallery\GalleryRepository.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\GalleryQuery;
use Mdev\Gb\PostGallery\GalleryRepository;
use Mdev\Gb\PostGallery\GalleryResult;
use Mdev\Gb\PostGallery\PostCardMapper;
use WP_Query;

/**
 * Object-cached queries.
 * Techniques: a fake WP_Query factory (no database), Brain Monkey mocks with
 * call expectations for the cache API, and a spy on thumbnail priming.
 */
final class GalleryRepositoryTest extends GalleryTestCase {

	/**
	 * Arguments the fake factory received.
	 *
	 * @var list<array<string, mixed>>
	 */
	private array $queries = [];

	/**
	 * Stub card functions and the cache stamp.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->stub_card_functions();
		$this->queries = [];
		Functions\when( 'wp_cache_get_last_changed' )->justReturn( '111' );
	}

	/**
	 * Repository with a fake query factory returning two posts.
	 *
	 * @return GalleryRepository
	 */
	private function repository(): GalleryRepository {
		return new GalleryRepository(
			function ( array $args ): WP_Query {
				$this->queries[]      = $args;
				$query                = new WP_Query( $args );
				$query->posts         = [ $this->post( 2 ), 'not-a-post', $this->post( 3 ) ];
				$query->found_posts   = 5;
				$query->max_num_pages = 3;
				return $query;
			},
			new PostCardMapper()
		);
	}

	/**
	 * Cache miss: query once, map posts, prime thumbnails, store the page.
	 */
	public function test_cache_miss_queries_and_stores(): void {
		$primed = 0;
		Functions\when( 'update_post_thumbnail_cache' )->alias(
			static function () use ( &$primed ): void {
				++$primed;
			}
		);
		Functions\expect( 'wp_cache_get' )->once()->andReturn( false );
		Functions\expect( 'wp_cache_set' )
			->once()
			->with( \Mockery::pattern( '/^post_gallery:[a-f0-9]{32}$/' ), \Mockery::type( 'array' ), GalleryRepository::CACHE_GROUP, GalleryRepository::CACHE_TTL )
			->andReturn( true );

		$query  = GalleryQuery::from_input( $this->settings(), [] );
		$result = $this->repository()->find( $query );

		self::assertSame( [ $query->to_query_args() ], $this->queries );
		self::assertSame( [ 2, 3 ], array_column( $result->items, 'id' ), 'Non-post rows are skipped.' );
		self::assertTrue( $result->has_more() );
		self::assertSame( 1, $primed );
	}

	/**
	 * Cache hit: the database is never touched.
	 */
	public function test_cache_hit_skips_query(): void {
		Functions\expect( 'wp_cache_get' )->once()->andReturn( ( new GalleryResult( [], 7, 1, 1 ) )->to_array() );
		Functions\expect( 'wp_cache_set' )->never();

		$result = $this->repository()->find( GalleryQuery::from_input( $this->settings(), [] ) );

		self::assertSame( [], $this->queries );
		self::assertSame( 7, $result->total );
	}
}
