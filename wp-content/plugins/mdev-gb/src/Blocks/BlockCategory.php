<?php
/**
 * Block inserter category for mdev blocks.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\Blocks;

/**
 * Adds the `mdev` block category to the inserter.
 *
 * @since 0.1.0
 */
final class BlockCategory {

	/**
	 * Category slug referenced by each block.json `category`.
	 *
	 * @since 0.1.0
	 */
	public const SLUG = 'mdev';

	/**
	 * Prepend the mdev category. Hooked to `block_categories_all`.
	 *
	 * @since 0.1.0
	 *
	 * @param array<int, array<string, mixed>> $categories Registered block categories.
	 * @return array<int, array<string, mixed>> Categories including the mdev category.
	 */
	public function register( array $categories ): array {
		foreach ( $categories as $category ) {
			if ( isset( $category['slug'] ) && self::SLUG === $category['slug'] ) {
				return $categories;
			}
		}

		$mdev = [
			'slug'  => self::SLUG,
			'title' => __( 'mDev', 'mdev-gb' ),
			'icon'  => null,
		];

		return array_merge( [ $mdev ], array_values( $categories ) );
	}
}
