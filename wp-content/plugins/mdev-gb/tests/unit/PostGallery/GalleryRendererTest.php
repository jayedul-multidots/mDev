<?php
/**
 * Covers Mdev\Gb\PostGallery\GalleryRenderer with views/post-gallery.php.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\GalleryRenderer;
use Mdev\Gb\PostGallery\GalleryRepository;
use Mdev\Gb\PostGallery\PostCardMapper;
use Mdev\Gb\PostGallery\ResultSummary;
use WP_Query;

/**
 * Server render through the real template.
 * Techniques: core helpers faked with WordPress-faithful behaviour (the
 * selected() fake casts to string like core, which is what catches enum
 * misuse), and an injected fake of the request's query parameters.
 */
final class GalleryRendererTest extends GalleryTestCase {

	/**
	 * Fake current query parameters.
	 *
	 * @var array<string, mixed>
	 */
	private array $params = [];

	/**
	 * Fake the rendering helpers.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->stub_card_functions();
		$this->params = [];

		Functions\stubs(
			[
				'wp_cache_get'                     => false,
				'wp_cache_set'                     => true,
				'wp_cache_get_last_changed'        => '1',
				'update_post_thumbnail_cache'      => null,
				'esc_url'                          => static fn ( string $url ): string => htmlspecialchars( $url, ENT_QUOTES ),
				'rest_url'                         => static fn ( string $path ): string => 'https://example.test/wp-json/' . $path,
				'tag_escape'                       => static fn ( string $tag ): string => $tag,
				'get_block_wrapper_attributes'     => static fn ( array $extra ): string => 'style="' . esc_attr( $extra['style'] ?? '' ) . '"',
				'wp_interactivity_data_wp_context' => static fn ( array $context ): string => "data-wp-context='" . esc_attr( (string) json_encode( $context ) ) . "'",
				'selected'                         => static function ( $selected, $current ): void {
					echo (string) $selected === (string) $current ? " selected='selected'" : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- test fake of core selected().
				},
			]
		);
	}

	/**
	 * Renderer with a fake query (5 results, 2 per page).
	 *
	 * @return GalleryRenderer
	 */
	private function renderer(): GalleryRenderer {
		$repository = new GalleryRepository(
			static function ( array $args ): WP_Query {
				$query                = new WP_Query( $args );
				$query->found_posts   = 5;
				$query->max_num_pages = 3;
				return $query;
			},
			new PostCardMapper()
		);

		return new GalleryRenderer( $repository, new ResultSummary(), __DIR__ . '/../../../views/post-gallery.php', fn (): array => $this->params );
	}

	/**
	 * URL state drives the render; the search value is escaped; the current
	 * sort is selected; the store context continues from the same state.
	 */
	public function test_renders_from_url_state(): void {
		$this->params = [
			'pg1_q'    => '"><img src=x onerror=alert(1)>river',
			'pg1_sort' => 'title-desc',
		];

		$html = $this->renderer()->render( [] );

		self::assertStringContainsString( 'data-wp-interactive="' . GalleryRenderer::STORE . '"', $html );
		self::assertStringNotContainsString( '<img src=x', $html );
		self::assertMatchesRegularExpression( "/value=\"title-desc\"\\s+selected='selected'/", $html );
		self::assertStringContainsString( 'href="?pg1_q=%22%3Eriver&amp;pg1_sort=title-desc&amp;pg1_page=2"', $html );

		preg_match( "/data-wp-context='([^']*)'/", $html, $match );
		$context = json_decode( html_entity_decode( $match[1], ENT_QUOTES ), true );
		self::assertSame( '">river', $context['search'] );
		self::assertSame( 'title-desc', $context['loadedSort'] );
		self::assertTrue( $context['hasMore'] );
	}

	/**
	 * Two galleries on one page get their own prefix, IDs and URL state.
	 */
	public function test_multiple_instances_are_independent(): void {
		$this->params = [ 'pg2_q' => 'second' ];
		$renderer     = $this->renderer();

		$first  = $renderer->render( [] );
		$second = $renderer->render( [] );

		self::assertStringContainsString( 'name="pg1_q"', $first );
		self::assertStringContainsString( 'id="mdev-pg-2-search"', $second );
		self::assertStringContainsString( 'value="second"', $second );
		self::assertStringNotContainsString( 'value="second"', $first );
	}
}
