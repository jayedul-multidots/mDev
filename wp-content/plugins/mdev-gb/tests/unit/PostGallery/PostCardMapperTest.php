<?php
/**
 * Covers Mdev\Gb\PostGallery\PostCardMapper video cards.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\PostCardMapper;

/**
 * Cards link to the permalink unless VIDEO_FILTER supplies a valid video.
 */
final class PostCardMapperTest extends GalleryTestCase {

	/**
	 * Stub card functions and a permissive esc_url_raw (http/https only).
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->stub_card_functions();
		Functions\when( 'esc_url_raw' )->alias(
			static fn ( string $url ): string => 1 === preg_match( '#^https?://#', $url ) ? $url : ''
		);
	}

	/**
	 * Without a filter, cards have no video and link to the post.
	 */
	public function test_card_without_video_links_to_permalink(): void {
		$card = ( new PostCardMapper() )->map( $this->post( 7 ), 'medium' );

		self::assertNull( $card['video'] );
		self::assertSame( 'https://example.test/?p=7', $card['url'] );
	}

	/**
	 * A supplied video replaces the permalink and is normalized.
	 */
	public function test_video_replaces_permalink(): void {
		Filters\expectApplied( PostCardMapper::VIDEO_FILTER )
			->once()
			->andReturn(
				[
					'url'  => 'https://example.test/v.mp4',
					'type' => 'Video/MP4',
					'id'   => '42',
				]
			);

		$card = ( new PostCardMapper() )->map( $this->post( 7 ), 'medium' );

		self::assertSame( 'https://example.test/v.mp4', $card['url'] );
		self::assertSame(
			[
				'url'  => 'https://example.test/v.mp4',
				'type' => 'video/mp4',
				'id'   => 42,
			],
			$card['video']
		);
	}

	/**
	 * Unknown type and id become null, so their data attributes are omitted.
	 */
	public function test_missing_type_and_id_are_null(): void {
		Filters\expectApplied( PostCardMapper::VIDEO_FILTER )
			->andReturn(
				[
					'url'  => 'https://example.test/v.mp4',
					'type' => 'not a mime" onclick="x',
					'id'   => 0,
				]
			);

		$video = ( new PostCardMapper() )->map( $this->post( 7 ), 'medium' )['video'];

		self::assertNotNull( $video );
		self::assertNull( $video['type'] );
		self::assertNull( $video['id'] );
	}

	/**
	 * Invalid filter results fall back to the permalink.
	 *
	 * @param mixed $video Filter result.
	 */
	#[\PHPUnit\Framework\Attributes\DataProvider( 'invalid_videos' )]
	public function test_invalid_video_falls_back_to_permalink( mixed $video ): void {
		Filters\expectApplied( PostCardMapper::VIDEO_FILTER )->andReturn( $video );

		$card = ( new PostCardMapper() )->map( $this->post( 7 ), 'medium' );

		self::assertNull( $card['video'] );
		self::assertSame( 'https://example.test/?p=7', $card['url'] );
	}

	/**
	 * Invalid filter results.
	 *
	 * @return array<string, array{mixed}>
	 */
	public static function invalid_videos(): array {
		return [
			'string'        => [ 'https://example.test/v.mp4' ],
			'no url'        => [ [ 'type' => 'video/mp4' ] ],
			'unsafe scheme' => [ [ 'url' => 'javascript:alert(1)' ] ],
		];
	}
}
