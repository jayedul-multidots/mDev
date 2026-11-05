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
 * Cards can carry a video (see VIDEO_FILTER): its URL then replaces the
 * permalink as the card link, and the link exposes `data-video-url`,
 * `data-video-type` and `data-video-id` for a theme to open it in a player.
 *
 * @since 0.2.0
 *
 * @phpstan-type Image array{src: string, srcset: string, sizes: string, width: int, height: int}
 * @phpstan-type Video array{url: string, type: string|null, id: int|null}
 * @phpstan-type Card array{id: int, title: string, url: string, excerpt: string, date: string, datetime: string, image: Image|null, video: Video|null}
 */
final class PostCardMapper {

	public const EXCERPT_WORDS = 24;

	/**
	 * Filters the video a card plays instead of linking to the post.
	 *
	 * Return null (default) to link to the permalink, or an array with `url`
	 * (required, the video file), `type` (MIME type, optional) and `id`
	 * (attachment ID, optional).
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed>|null $video Video, or null.
	 * @param WP_Post                   $post  Post being mapped.
	 */
	public const VIDEO_FILTER = 'mdev_gb_post_gallery_card_video';

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
		$video     = $this->video( $post );

		return [
			'id'       => (int) $post->ID,
			'title'    => '' !== $title ? $title : __( '(no title)', 'mdev-gb' ),
			'url'      => null !== $video ? $video['url'] : esc_url_raw( (string) get_permalink( $post ) ),
			'excerpt'  => $this->plain_text( wp_trim_words( get_the_excerpt( $post ), self::EXCERPT_WORDS, '…' ) ),
			'date'     => $timestamp > 0 ? (string) wp_date( (string) get_option( 'date_format' ), $timestamp ) : '',
			'datetime' => $timestamp > 0 ? (string) wp_date( 'c', $timestamp ) : '',
			'image'    => $this->image( $post, $image_size ),
			'video'    => $video,
		];
	}

	/**
	 * Video supplied through VIDEO_FILTER, validated; null when there is none.
	 * Unknown type/id are null so their data attributes are left out.
	 *
	 * @param WP_Post $post Post.
	 * @return Video|null
	 */
	private function video( WP_Post $post ): ?array {
		// Literal hook name (= self::VIDEO_FILTER) so tooling can find and check it.
		$video = apply_filters( 'mdev_gb_post_gallery_card_video', null, $post );
		if ( ! is_array( $video ) || ! is_string( $video['url'] ?? null ) ) {
			return null;
		}

		$url = esc_url_raw( $video['url'], [ 'http', 'https' ] );
		if ( '' === $url ) {
			return null;
		}

		$type = is_string( $video['type'] ?? null ) ? $video['type'] : '';
		$id   = is_numeric( $video['id'] ?? null ) ? (int) $video['id'] : 0;

		return [
			'url'  => $url,
			'type' => 1 === preg_match( '#^[a-z0-9.+-]+/[a-z0-9.+-]+$#i', $type ) ? strtolower( $type ) : null,
			'id'   => $id > 0 ? $id : null,
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
