<?php
/**
 * Normalized post gallery configuration.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * Immutable, validated gallery configuration built from block attributes or
 * REST parameters. Every value is clamped or allowlisted here, so the rest of
 * the feature can trust it.
 *
 * @since 0.2.0
 */
final class GallerySettings {

	public const DEFAULT_POST_TYPE  = 'post';
	public const DEFAULT_PER_PAGE   = 9;
	public const MAX_PER_PAGE       = 24;
	public const DEFAULT_COLUMNS    = 3;
	public const MAX_COLUMNS        = 6;
	public const MAX_CATEGORIES     = 20;
	public const DEFAULT_HEADING    = 3;
	public const DEFAULT_IMAGE_SIZE = 'medium_large';

	/**
	 * Constructor. Use the named constructors; values are not re-validated here.
	 *
	 * @since 0.2.0
	 *
	 * @param string     $post_type     Public, viewable post type.
	 * @param int        $per_page      Items per request (1..MAX_PER_PAGE).
	 * @param int        $columns       Grid columns (1..MAX_COLUMNS).
	 * @param int[]      $categories    Category term IDs (empty: all).
	 * @param SortOption $default_sort  Sort used when the visitor picks none.
	 * @param bool       $show_search   Render the search field.
	 * @param bool       $show_sort     Render the sort control.
	 * @param bool       $show_excerpt  Render excerpts.
	 * @param bool       $show_date     Render dates.
	 * @param int        $heading_level Card heading level (2..6).
	 * @param string     $image_size    Registered image size.
	 *
	 * @phpstan-param list<int> $categories
	 */
	public function __construct(
		public readonly string $post_type,
		public readonly int $per_page,
		public readonly int $columns,
		public readonly array $categories,
		public readonly SortOption $default_sort,
		public readonly bool $show_search,
		public readonly bool $show_sort,
		public readonly bool $show_excerpt,
		public readonly bool $show_date,
		public readonly int $heading_level,
		public readonly string $image_size,
	) {
	}

	/**
	 * Build from (untrusted) block attributes or REST parameters.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $attributes Raw attributes, camelCase keys as in block.json.
	 * @return GallerySettings
	 */
	public static function from_attributes( array $attributes ): GallerySettings {
		$post_type = self::sanitize_post_type( $attributes['postType'] ?? null );

		return new self(
			$post_type,
			self::clamp( $attributes['perPage'] ?? null, 1, self::MAX_PER_PAGE, self::DEFAULT_PER_PAGE ),
			self::clamp( $attributes['columns'] ?? null, 1, self::MAX_COLUMNS, self::DEFAULT_COLUMNS ),
			self::sanitize_categories( $attributes['categories'] ?? [], $post_type ),
			SortOption::from_input( $attributes['defaultSort'] ?? null ),
			self::to_bool( $attributes['showSearch'] ?? true ),
			self::to_bool( $attributes['showSort'] ?? true ),
			self::to_bool( $attributes['showExcerpt'] ?? true ),
			self::to_bool( $attributes['showDate'] ?? true ),
			self::clamp( $attributes['headingLevel'] ?? null, 2, 6, self::DEFAULT_HEADING ),
			self::sanitize_image_size( $attributes['imageSize'] ?? null ),
		);
	}

	/**
	 * Whether a post type may be listed: registered, publicly viewable, and
	 * not attachments (media has no useful public archive card).
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $post_type Raw post type slug.
	 * @return bool
	 */
	public static function is_allowed_post_type( mixed $post_type ): bool {
		if ( ! is_string( $post_type ) || '' === $post_type || 'attachment' === $post_type ) {
			return false;
		}

		return post_type_exists( $post_type ) && is_post_type_viewable( $post_type );
	}

	/**
	 * Settings the browser sends back to the REST endpoint.
	 *
	 * @since 0.2.0
	 *
	 * @return array{postType: string, perPage: int, categories: list<int>, imageSize: string}
	 */
	public function to_request_params(): array {
		return [
			'postType'   => $this->post_type,
			'perPage'    => $this->per_page,
			'categories' => $this->categories,
			'imageSize'  => $this->image_size,
		];
	}

	/**
	 * Sanitize a post type, falling back to posts.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function sanitize_post_type( mixed $value ): string {
		$post_type = is_string( $value ) ? sanitize_key( $value ) : '';

		return self::is_allowed_post_type( $post_type ) ? $post_type : self::DEFAULT_POST_TYPE;
	}

	/**
	 * Unique positive category IDs, only for post types using categories.
	 *
	 * @param mixed  $value     Raw value: list of IDs or comma-separated string.
	 * @param string $post_type Validated post type.
	 * @return list<int>
	 */
	private static function sanitize_categories( mixed $value, string $post_type ): array {
		if ( is_string( $value ) ) {
			$value = explode( ',', $value );
		}
		if ( ! is_array( $value ) || [] === $value || ! is_object_in_taxonomy( $post_type, 'category' ) ) {
			return [];
		}

		$ids = array_filter(
			array_map( static fn ( mixed $id ): int => is_numeric( $id ) ? (int) $id : 0, $value ),
			static fn ( int $id ): bool => $id > 0
		);

		return array_slice( array_values( array_unique( $ids ) ), 0, self::MAX_CATEGORIES );
	}

	/**
	 * Registered image size, falling back to the default.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function sanitize_image_size( mixed $value ): string {
		if ( ! is_string( $value ) ) {
			return self::DEFAULT_IMAGE_SIZE;
		}
		if ( 'full' === $value || in_array( $value, get_intermediate_image_sizes(), true ) ) {
			return $value;
		}

		return self::DEFAULT_IMAGE_SIZE;
	}

	/**
	 * Integer within bounds; non-numeric input uses the default.
	 *
	 * @param mixed $value    Raw value.
	 * @param int   $min      Lower bound.
	 * @param int   $max      Upper bound.
	 * @param int   $fallback Fallback for non-numeric input.
	 * @return int
	 */
	private static function clamp( mixed $value, int $min, int $max, int $fallback ): int {
		if ( ! is_numeric( $value ) ) {
			return $fallback;
		}

		return max( $min, min( $max, (int) $value ) );
	}

	/**
	 * Boolean from attribute/REST style values ("false" and "0" are false).
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	private static function to_bool( mixed $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}
}
