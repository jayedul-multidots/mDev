<?php
/**
 * Sort options for the post gallery.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * Allowlisted sort orders, mapped to WP_Query `orderby` / `order`.
 *
 * Keep the values in sync with `blocks/post-gallery/lib/sort-options.js` and
 * the `defaultSort` enum in `blocks/post-gallery/block.json`.
 *
 * @since 0.2.0
 */
enum SortOption: string {

	case Relevance     = 'relevance';
	case Newest        = 'newest';
	case Oldest        = 'oldest';
	case TitleAsc      = 'title-asc';
	case TitleDesc     = 'title-desc';
	case MostDiscussed = 'most-discussed';

	/**
	 * Parse untrusted input, falling back when it is not an allowed value.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed      $value    Raw value (request parameter, block attribute).
	 * @param SortOption $fallback Value used for invalid input.
	 * @return SortOption
	 */
	public static function from_input( mixed $value, SortOption $fallback = self::Newest ): SortOption {
		if ( ! is_string( $value ) ) {
			return $fallback;
		}

		return self::tryFrom( strtolower( trim( $value ) ) ) ?? $fallback;
	}

	/**
	 * WP_Query ordering arguments.
	 *
	 * Relevance only makes sense for a search; without one it falls back to
	 * newest first so results stay deterministic.
	 *
	 * @since 0.2.0
	 *
	 * @param bool $has_search Whether the query includes a search term.
	 * @return array{orderby: string, order: string}
	 */
	public function query_order( bool $has_search ): array {
		return match ( $this ) {
			self::Relevance     => $has_search
				? [
					'orderby' => 'relevance',
					'order'   => 'DESC',
				]
				: self::Newest->query_order( false ),
			self::Newest        => [
				'orderby' => 'date',
				'order'   => 'DESC',
			],
			self::Oldest        => [
				'orderby' => 'date',
				'order'   => 'ASC',
			],
			self::TitleAsc      => [
				'orderby' => 'title',
				'order'   => 'ASC',
			],
			self::TitleDesc     => [
				'orderby' => 'title',
				'order'   => 'DESC',
			],
			self::MostDiscussed => [
				'orderby' => 'comment_count',
				'order'   => 'DESC',
			],
		};
	}

	/**
	 * Translated label for the sort control.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public function label(): string {
		return match ( $this ) {
			self::Relevance     => __( 'Best match', 'mdev-gb' ),
			self::Newest        => __( 'Newest first', 'mdev-gb' ),
			self::Oldest        => __( 'Oldest first', 'mdev-gb' ),
			self::TitleAsc      => __( 'Title (A–Z)', 'mdev-gb' ),
			self::TitleDesc     => __( 'Title (Z–A)', 'mdev-gb' ),
			self::MostDiscussed => __( 'Most discussed', 'mdev-gb' ),
		};
	}

	/**
	 * All option values, in display order.
	 *
	 * @since 0.2.0
	 *
	 * @return list<string>
	 */
	public static function values(): array {
		return array_map( static fn ( SortOption $option ): string => $option->value, self::cases() );
	}
}
