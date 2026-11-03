<?php
/**
 * Query-string state for a gallery instance.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * Reads and writes a gallery's state in the page URL, e.g.
 * `?pg1_q=coffee&pg1_sort=oldest&pg1_page=2`. The per-instance prefix keeps
 * several galleries on one page independent.
 *
 * This powers the no-JavaScript fallback (a plain GET form and "Load more"
 * link) and shareable URLs. `blocks/post-gallery/lib/url-state.js` mirrors it.
 *
 * @since 0.2.0
 */
final class UrlParams {

	public const SEARCH = 'q';
	public const SORT   = 'sort';
	public const PAGE   = 'page';

	/**
	 * Prefix for the Nth gallery rendered on a page.
	 *
	 * @since 0.2.0
	 *
	 * @param int $instance 1-based render order.
	 * @return string
	 */
	public static function prefix( int $instance ): string {
		return 'pg' . max( 1, $instance ) . '_';
	}

	/**
	 * Extract this gallery's raw (unsanitized) input from query parameters.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $params Query parameters (already unslashed).
	 * @param string               $prefix Instance prefix.
	 * @return array{search?: mixed, sort?: mixed, page?: mixed}
	 */
	public static function read( array $params, string $prefix ): array {
		$input = [];
		foreach ( [
			'search' => self::SEARCH,
			'sort'   => self::SORT,
			'page'   => self::PAGE,
		] as $field => $key ) {
			if ( array_key_exists( $prefix . $key, $params ) ) {
				$input[ $field ] = $params[ $prefix . $key ];
			}
		}

		return $input;
	}

	/**
	 * Relative URL (query string only) for a given gallery state.
	 *
	 * Other query parameters are preserved; defaults are omitted to keep URLs
	 * short and canonical.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $params Current query parameters.
	 * @param string               $prefix Instance prefix.
	 * @param GalleryQuery         $query  Desired state.
	 * @return string e.g. `?pg1_q=coffee&pg1_page=2`, or `?` when nothing is set.
	 */
	public static function build( array $params, string $prefix, GalleryQuery $query ): string {
		unset( $params[ $prefix . self::SEARCH ], $params[ $prefix . self::SORT ], $params[ $prefix . self::PAGE ] );

		if ( '' !== $query->search ) {
			$params[ $prefix . self::SEARCH ] = $query->search;
		}
		if ( $query->sort !== $query->settings->default_sort ) {
			$params[ $prefix . self::SORT ] = $query->sort->value;
		}
		if ( $query->page > 1 ) {
			$params[ $prefix . self::PAGE ] = (string) $query->page;
		}

		return '?' . http_build_query( $params, '', '&', PHP_QUERY_RFC3986 );
	}
}
