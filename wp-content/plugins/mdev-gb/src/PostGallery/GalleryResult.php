<?php
/**
 * One page of gallery results.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * Immutable result page; also the shape cached and sent over REST.
 *
 * @since 0.2.0
 *
 * @phpstan-import-type Card from PostCardMapper
 */
final class GalleryResult {

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param array $items       Cards on this page.
	 * @param int   $total       Total matching posts.
	 * @param int   $total_pages Total pages.
	 * @param int   $page        This page number.
	 *
	 * @phpstan-param list<Card> $items
	 */
	public function __construct(
		public readonly array $items,
		public readonly int $total,
		public readonly int $total_pages,
		public readonly int $page,
	) {
	}

	/**
	 * Whether another page follows this one.
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public function has_more(): bool {
		return $this->page < $this->total_pages;
	}

	/**
	 * Array form (REST response body, cache value).
	 *
	 * @since 0.2.0
	 *
	 * @return array{items: list<Card>, total: int, totalPages: int, page: int, hasMore: bool}
	 */
	public function to_array(): array {
		return [
			'items'      => $this->items,
			'total'      => $this->total,
			'totalPages' => $this->total_pages,
			'page'       => $this->page,
			'hasMore'    => $this->has_more(),
		];
	}

	/**
	 * Rebuild from to_array() output (e.g. a cache hit). Returns null for
	 * anything malformed so callers fall back to a fresh query.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $data Cached value.
	 * @return GalleryResult|null
	 */
	public static function from_array( mixed $data ): ?GalleryResult {
		if ( ! is_array( $data ) || ! isset( $data['items'], $data['total'], $data['totalPages'], $data['page'] ) || ! is_array( $data['items'] ) ) {
			return null;
		}

		/**
		 * Card shape is guaranteed by to_array().
		 *
		 * @var list<Card> $items
		 */
		$items = array_values( $data['items'] );

		return new self( $items, (int) $data['total'], (int) $data['totalPages'], (int) $data['page'] );
	}
}
