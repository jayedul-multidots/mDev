<?php
/**
 * Cached gallery queries.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

use Closure;
use WP_Post;
use WP_Query;

/**
 * Runs gallery queries through the object cache.
 *
 * Cache keys include the `posts` last-changed stamp, so publishing, editing or
 * deleting any post invalidates every gallery entry at once without tracking
 * individual keys. The TTL bounds memory used by one-off search terms.
 *
 * @since 0.2.0
 */
final class GalleryRepository {

	public const CACHE_GROUP = 'mdev_gb';
	public const CACHE_TTL   = 5 * MINUTE_IN_SECONDS;

	/**
	 * Builds a WP_Query from arguments (injected for testability).
	 *
	 * @var Closure(array<string, mixed>): WP_Query
	 */
	private Closure $query_factory;

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param Closure        $query_factory Creates and runs the query.
	 * @param PostCardMapper $mapper        Card mapper.
	 *
	 * @phpstan-param Closure(array<string, mixed>): WP_Query $query_factory
	 */
	public function __construct( Closure $query_factory, private readonly PostCardMapper $mapper ) {
		$this->query_factory = $query_factory;
	}

	/**
	 * Fetch one page of results.
	 *
	 * @since 0.2.0
	 *
	 * @param GalleryQuery $query Validated query.
	 * @return GalleryResult
	 */
	public function find( GalleryQuery $query ): GalleryResult {
		$key    = $this->cache_key( $query );
		$cached = GalleryResult::from_array( wp_cache_get( $key, self::CACHE_GROUP ) );
		if ( null !== $cached ) {
			return $cached;
		}

		$wp_query = ( $this->query_factory )( $query->to_query_args() );
		$posts    = array_values(
			array_filter( (array) $wp_query->posts, static fn ( mixed $post ): bool => $post instanceof WP_Post )
		);

		// One query for all thumbnails instead of one per card.
		update_post_thumbnail_cache( $wp_query );

		$result = new GalleryResult(
			array_map( fn ( WP_Post $post ): array => $this->mapper->map( $post, $query->settings->image_size ), $posts ),
			(int) $wp_query->found_posts,
			(int) $wp_query->max_num_pages,
			$query->page,
		);

		wp_cache_set( $key, $result->to_array(), self::CACHE_GROUP, self::CACHE_TTL );

		return $result;
	}

	/**
	 * Versioned cache key.
	 *
	 * @param GalleryQuery $query Query.
	 * @return string
	 */
	private function cache_key( GalleryQuery $query ): string {
		return 'post_gallery:' . md5( $query->cache_key() . ':' . wp_cache_get_last_changed( 'posts' ) );
	}
}
