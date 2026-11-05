<?php
/**
 * Covers Mdev\Gb\PostGallery\GallerySettings post type resolution.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\GallerySettings;

/**
 * The gallery lists videos when the video post type is registered, and posts
 * otherwise. The base case registers only `post` and `page`.
 */
final class GallerySettingsTest extends GalleryTestCase {

	/**
	 * Register the video post type as public, without the category taxonomy.
	 */
	private function register_videos(): void {
		Functions\when( 'post_type_exists' )->alias( static fn ( string $type ): bool => in_array( $type, [ 'post', 'page', 'attachment', GallerySettings::DEFAULT_POST_TYPE ], true ) );
		Functions\when( 'is_post_type_viewable' )->alias( static fn ( string $type ): bool => in_array( $type, [ 'post', 'page', 'attachment', GallerySettings::DEFAULT_POST_TYPE ], true ) );
	}

	/**
	 * Videos are the default when registered.
	 */
	public function test_defaults_to_videos_when_registered(): void {
		$this->register_videos();

		self::assertSame( GallerySettings::DEFAULT_POST_TYPE, GallerySettings::default_post_type() );
		self::assertSame( GallerySettings::DEFAULT_POST_TYPE, $this->settings()->post_type );
		self::assertSame( GallerySettings::DEFAULT_POST_TYPE, $this->settings( [ 'postType' => 'attachment' ] )->post_type );
	}

	/**
	 * An explicitly chosen post type is kept.
	 */
	public function test_explicit_post_type_is_kept(): void {
		$this->register_videos();

		self::assertSame( 'post', $this->settings( [ 'postType' => 'post' ] )->post_type );
	}

	/**
	 * Categories only apply to post types using the category taxonomy.
	 */
	public function test_categories_are_dropped_for_videos(): void {
		$this->register_videos();

		self::assertSame( [], $this->settings( [ 'categories' => [ 3 ] ] )->categories );
	}

	/**
	 * Without the video post type (e.g. another theme active), posts are used.
	 */
	public function test_falls_back_to_posts_when_videos_are_not_registered(): void {
		self::assertSame( GallerySettings::FALLBACK_POST_TYPE, GallerySettings::default_post_type() );
		self::assertSame( GallerySettings::FALLBACK_POST_TYPE, $this->settings()->post_type );
	}
}
