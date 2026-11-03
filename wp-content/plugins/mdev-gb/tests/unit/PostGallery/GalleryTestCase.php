<?php
/**
 * Shared setup for post gallery unit tests.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\PostGallery;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mdev\Gb\PostGallery\GallerySettings;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Stubs the WordPress functions the gallery relies on with realistic
 * behaviour: `post` and `page` are public; only `post` uses categories.
 */
abstract class GalleryTestCase extends TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Set up Brain Monkey and common stubs.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\stubTranslationFunctions();
		Functions\stubEscapeFunctions();
		Functions\stubs(
			[
				'post_type_exists'            => static fn ( string $type ): bool => in_array( $type, [ 'post', 'page', 'attachment', 'private_thing' ], true ),
				'is_post_type_viewable'       => static fn ( string $type ): bool => in_array( $type, [ 'post', 'page', 'attachment' ], true ),
				'is_object_in_taxonomy'       => static fn ( string $type, string $taxonomy ): bool => 'post' === $type && 'category' === $taxonomy,
				'get_intermediate_image_sizes' => static fn (): array => [ 'thumbnail', 'medium', 'medium_large', 'large' ],
				'sanitize_key'                => static fn ( string $key ): string => (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $key ) ),
				'sanitize_text_field'         => static fn ( string $text ): string => trim( (string) preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( $text ) ) ),
				'wp_json_encode'              => static fn ( mixed $data ): string|false => json_encode( $data ),
				'number_format_i18n'          => static fn ( float|int $number ): string => number_format( (float) $number ),
				'wp_unslash'                  => static fn ( mixed $value ): mixed => is_array( $value ) ? array_map( 'stripslashes', $value ) : stripslashes( (string) $value ),
			]
		);
	}

	/**
	 * Tear down Brain Monkey.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Settings from attributes.
	 *
	 * @param array<string, mixed> $attributes Overrides.
	 * @return GallerySettings
	 */
	protected function settings( array $attributes = [] ): GallerySettings {
		return GallerySettings::from_attributes( $attributes );
	}

	/**
	 * Stub the functions PostCardMapper calls, deriving values from the post ID.
	 * Posts with even IDs have a featured image.
	 */
	protected function stub_card_functions(): void {
		Functions\stubs(
			[
				'get_the_title'                => static fn ( WP_Post $post ): string => $post->post_title,
				'get_permalink'                => static fn ( WP_Post $post ): string => 'https://example.test/?p=' . $post->ID,
				'get_the_excerpt'              => static fn ( WP_Post $post ): string => 'Excerpt ' . $post->ID,
				'wp_trim_words'                => static fn ( string $text ): string => $text,
				'wp_strip_all_tags'            => static fn ( string $text ): string => strip_tags( $text ),
				'get_post_timestamp'           => static fn ( WP_Post $post ): int => 1_700_000_000 + $post->ID,
				'get_option'                   => static fn ( string $name ): string => 'date_format' === $name ? 'Y-m-d' : '',
				'wp_date'                      => static fn ( string $format, int $timestamp ): string => gmdate( $format, $timestamp ),
				'get_post_thumbnail_id'        => static fn ( WP_Post $post ): int => 0 === $post->ID % 2 ? 1000 + $post->ID : 0,
				'wp_get_attachment_image_src'  => static fn ( int $id ): array => [ 'https://example.test/img-' . $id . '.jpg', 768, 480, true ],
				'wp_get_attachment_image_srcset' => static fn (): string => '',
				'wp_get_attachment_image_sizes' => static fn (): string => '',
			]
		);
	}

	/**
	 * A post stand-in.
	 *
	 * @param int    $id    Post ID.
	 * @param string $title Title.
	 * @return WP_Post
	 */
	protected function post( int $id, string $title = '' ): WP_Post {
		return new WP_Post(
			[
				'ID'         => $id,
				'post_title' => '' === $title ? 'Post ' . $id : $title,
			]
		);
	}
}
