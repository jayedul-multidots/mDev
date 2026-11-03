<?php
/**
 * Covers Mdev\Gb\PostGallery\GalleryQuery.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Mdev\Gb\PostGallery\GalleryQuery;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Untrusted input becomes bounded WP_Query arguments.
 * Technique: data provider (one test, many hostile inputs).
 */
final class GalleryQueryTest extends GalleryTestCase {

	/**
	 * Search terms become plain, single-spaced, length-capped text.
	 *
	 * @param mixed  $input    Raw search.
	 * @param string $expected Sanitized search.
	 */
	#[DataProvider( 'search_inputs' )]
	public function test_search_is_sanitized( mixed $input, string $expected ): void {
		self::assertSame( $expected, GalleryQuery::from_input( $this->settings(), [ 'search' => $input ] )->search );
	}

	/**
	 * Search inputs.
	 *
	 * @return array<string, array{mixed, string}>
	 */
	public static function search_inputs(): array {
		return [
			'whitespace'   => [ "  coffee \t beans ", 'coffee beans' ],
			'markup'       => [ '<script>alert(1)</script>beans', 'alert(1)beans' ],
			'not a string' => [ [ 'coffee' ], '' ],
			'too long'     => [ str_repeat( 'ü', 150 ), str_repeat( 'ü', GalleryQuery::MAX_SEARCH_LENGTH ) ],
		];
	}

	/**
	 * Hostile values are clamped/allowlisted before reaching WP_Query.
	 */
	public function test_hostile_input_produces_safe_query_args(): void {
		$settings = $this->settings(
			[
				'postType'   => 'attachment',
				'perPage'    => 9999,
				'categories' => [ 3, '3', -1, 'x' ],
			]
		);
		$args     = GalleryQuery::from_input(
			$settings,
			[
				'sort'   => 'rand',
				'page'   => 999999,
				'search' => 'river',
			]
		)->to_query_args();

		self::assertSame( 'post', $args['post_type'] );
		self::assertSame( 'publish', $args['post_status'] );
		self::assertFalse( $args['has_password'] );
		self::assertSame( 24, $args['posts_per_page'] );
		self::assertSame( GalleryQuery::MAX_PAGE, $args['paged'] );
		self::assertSame( 'date', $args['orderby'] );
		self::assertSame( [ 3 ], $args['category__in'] );
		self::assertSame( 'river', $args['s'] );
	}
}
