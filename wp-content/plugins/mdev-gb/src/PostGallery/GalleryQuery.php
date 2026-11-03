<?php
/**
 * A single, validated post gallery query.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * Visitor input (search, sort, page) combined with gallery settings, turned
 * into bounded WP_Query arguments.
 *
 * @since 0.2.0
 */
final class GalleryQuery {

	public const MAX_SEARCH_LENGTH = 100;

	/**
	 * Deep pagination is expensive (OFFSET scans); nobody pages this far.
	 */
	public const MAX_PAGE = 500;

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param GallerySettings $settings Validated gallery settings.
	 * @param string          $search   Sanitized search term ('' for none).
	 * @param SortOption      $sort     Sort order.
	 * @param int             $page     1-based page number.
	 */
	public function __construct(
		public readonly GallerySettings $settings,
		public readonly string $search,
		public readonly SortOption $sort,
		public readonly int $page,
	) {
	}

	/**
	 * Build from untrusted visitor input.
	 *
	 * @since 0.2.0
	 *
	 * @param GallerySettings      $settings Validated gallery settings.
	 * @param array<string, mixed> $input    Keys `search`, `sort`, `page` (all optional).
	 * @return GalleryQuery
	 */
	public static function from_input( GallerySettings $settings, array $input ): GalleryQuery {
		return new self(
			$settings,
			self::sanitize_search( $input['search'] ?? '' ),
			SortOption::from_input( $input['sort'] ?? null, $settings->default_sort ),
			self::sanitize_page( $input['page'] ?? 1 ),
		);
	}

	/**
	 * Same query for another page.
	 *
	 * @since 0.2.0
	 *
	 * @param int $page 1-based page number (clamped).
	 * @return GalleryQuery
	 */
	public function with_page( int $page ): GalleryQuery {
		return new self( $this->settings, $this->search, $this->sort, self::sanitize_page( $page ) );
	}

	/**
	 * WP_Query arguments. Published, non-password-protected posts only, with a
	 * hard cap on page size and only the caches the cards actually need.
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_query_args(): array {
		$has_search = '' !== $this->search;

		$args = [
			'post_type'              => $this->settings->post_type,
			'post_status'            => 'publish',
			'has_password'           => false,
			'ignore_sticky_posts'    => true,
			'posts_per_page'         => $this->settings->per_page,
			'paged'                  => $this->page,
			'no_found_rows'          => false,
			'update_post_term_cache' => false,
		] + $this->sort->query_order( $has_search );

		if ( $has_search ) {
			$args['s'] = $this->search;
		}
		if ( [] !== $this->settings->categories ) {
			$args['category__in'] = $this->settings->categories;
		}

		return $args;
	}

	/**
	 * Stable cache key fragment for this query.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public function cache_key(): string {
		return md5( (string) wp_json_encode( [ $this->to_query_args(), $this->settings->image_size ] ) );
	}

	/**
	 * Normalize a search term: plain text, single-spaced, length-capped.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function sanitize_search( mixed $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$search = (string) preg_replace( '/\s+/u', ' ', sanitize_text_field( $value ) );

		return trim( mb_substr( trim( $search ), 0, self::MAX_SEARCH_LENGTH ) );
	}

	/**
	 * Page number within 1..MAX_PAGE.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	private static function sanitize_page( mixed $value ): int {
		$page = is_numeric( $value ) ? (int) $value : 1;

		return max( 1, min( self::MAX_PAGE, $page ) );
	}
}
