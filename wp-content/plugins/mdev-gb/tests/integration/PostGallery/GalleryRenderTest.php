<?php
/**
 * Covers Mdev\Gb\PostGallery\GalleryRenderer and views/post-gallery.php
 * rendered by real WordPress: block registration from the build manifest,
 * do_blocks() and the server-side Interactivity API directive processing.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Integration\PostGallery;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * The mDev Post Gallery block as rendered on the front end.
 */
final class GalleryRenderTest extends WP_UnitTestCase {

	private const VIDEO_FILTER = 'mdev_gb_post_gallery_card_video';

	/**
	 * The block is registered with its server-side render callback.
	 */
	public function test_block_is_registered(): void {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'mdev-gb/post-gallery' );

		$this->assertNotNull( $block, 'Is the plugin built (make build)?' );
		$this->assertIsCallable( $block->render_callback );
		$this->assertSame( 'deep_pockets_videos', $block->attributes['postType']['default'] );
	}

	/**
	 * The first page of cards is rendered on the server (no JavaScript
	 * needed), linking to the posts, with titles escaped.
	 */
	public function test_renders_cards_on_the_server(): void {
		$post_id = self::factory()->post->create( [ 'post_title' => 'Tom & Jerry' ] );
		self::factory()->post->create( [ 'post_title' => 'Second post' ] );

		$html = $this->render();

		$this->assertStringContainsString( 'data-wp-interactive="mdev-gb/post-gallery"', $html );
		$this->assertStringContainsString( '>Tom &amp; Jerry</a>', $html );
		$this->assertStringContainsString( '>Second post</a>', $html );
		$this->assertStringContainsString( 'href="' . esc_url( (string) get_permalink( $post_id ) ) . '"', $html );
		$this->assertStringContainsString( '2 posts found.', $html );
	}

	/**
	 * Without a video, card links carry no data-video-* attributes at all
	 * (not even empty ones).
	 */
	public function test_cards_without_video_have_no_video_attributes(): void {
		self::factory()->post->create( [ 'post_title' => 'Plain post' ] );

		$html = $this->render();

		$this->assertDoesNotMatchRegularExpression( '/\sdata-video-(url|type|id)=/', $html );
	}

	/**
	 * A video from the filter becomes the card link and its data attributes;
	 * a missing attachment ID leaves data-video-id out.
	 */
	public function test_video_from_filter_is_rendered_on_card_links(): void {
		self::factory()->post->create( [ 'post_title' => 'Video post' ] );
		$video = static fn (): array => [
			'url'  => 'https://example.org/clip.mp4',
			'type' => 'video/mp4',
		];
		add_filter( self::VIDEO_FILTER, $video );

		$html = $this->render();

		// Image link and title link.
		$this->assertSame( 2, substr_count( $html, ' data-video-url="https://example.org/clip.mp4"' ) );
		$this->assertSame( 2, substr_count( $html, ' data-video-type="video/mp4"' ) );
		$this->assertStringContainsString( 'href="https://example.org/clip.mp4"', $html );
		$this->assertDoesNotMatchRegularExpression( '/\sdata-video-id=/', $html );
	}

	/**
	 * Render the block for posts.
	 *
	 * @return string HTML.
	 */
	private function render(): string {
		return do_blocks( '<!-- wp:mdev-gb/post-gallery {"postType":"post"} /-->' );
	}
}
