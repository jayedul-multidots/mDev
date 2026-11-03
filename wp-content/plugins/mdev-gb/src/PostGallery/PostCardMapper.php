<?php
/**
 * Maps posts to gallery cards.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

use WP_Post;

/**
 * Converts a WP_Post into the plain card array used by the server render, the
 * REST response and the browser store.
 *
 * Values are plain text / raw URLs (never HTML): they are escaped at output,
 * by esc_html/esc_attr on the server and textContent/setAttribute in the
 * browser.
 *
 * @since 0.2.0
 *
 * @phpstan-type Image array{src: string, srcset: string, sizes: string, width: int, height: int}
 * @phpstan-type Card array{id: int, title: string, url: string, excerpt: string, date: string, datetime: string, image: Image|null}
 */
final class PostCardMapper {

	public const EXCERPT_WORDS = 24;

	/**
	 * Map a post to a card.
	 *
	 * @since 0.2.0
	 *
	 * @param WP_Post $post       Post.
	 * @param string  $image_size Registered image size for the thumbnail.
	 * @return Card
	 */
	public function map( WP_Post $post, string $image_size ): array {
		$title     = $this->plain_text( get_the_title( $post ) );
		$timestamp = (int) get_post_timestamp( $post );

		return [
			'id'       => (int) $post->ID,
			'title'    => '' !== $title ? $title : __( '(no title)', 'mdev-gb' ),
			'url'      => esc_url_raw( (string) get_permalink( $post ) ),
			'excerpt'  => $this->plain_text( wp_trim_words( get_the_excerpt( $post ), self::EXCERPT_WORDS, '…' ) ),
			'date'     => $timestamp > 0 ? (string) wp_date( (string) get_option( 'date_format' ), $timestamp ) : '',
			'datetime' => $timestamp > 0 ? (string) wp_date( 'c', $timestamp ) : '',
			'image'    => $this->image( $post, $image_size ),
		];
	}

	/**
	 * Featured image data, or null when there is none (or it is missing).
	 *
	 * @param WP_Post $post       Post.
	 * @param string  $image_size Image size.
	 * @return Image|null
	 */
	private function image( WP_Post $post, string $image_size ): ?array {
		$attachment_id = (int) get_post_thumbnail_id( $post );
		if ( $attachment_id <= 0 ) {
			return null;
		}

		$src = wp_get_attachment_image_src( $attachment_id, $image_size );
		if ( ! is_array( $src ) || empty( $src[0] ) ) {
			return null;
		}

		return [
			'src'    => esc_url_raw( (string) $src[0] ),
			'srcset' => (string) wp_get_attachment_image_srcset( $attachment_id, $image_size ),
			'sizes'  => (string) wp_get_attachment_image_sizes( $attachment_id, $image_size ),
			'width'  => (int) $src[1],
			'height' => (int) $src[2],
		];
	}

	/**
	 * Strip tags and decode entities (titles come back texturized, e.g. &#8217;),
	 * so the value is safe as plain text and displays correctly once escaped.
	 *
	 * @param string $value Possibly HTML.
	 * @return string
	 */
	private function plain_text( string $value ): string {
		return trim( html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}
