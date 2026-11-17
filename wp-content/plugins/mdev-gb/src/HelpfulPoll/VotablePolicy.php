<?php
/**
 * Which posts accept helpful poll votes.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

use WP_Post;

/**
 * The poll is for posts and pages only, never custom post types. A post
 * accepts votes when it is one of those and anonymous visitors can read it:
 * published and not password protected. Drafts, private posts and previews
 * never collect votes.
 *
 * @since 0.3.0
 */
final class VotablePolicy {

	/**
	 * Post types the poll is available for.
	 *
	 * @since 0.3.0
	 *
	 * @var list<string>
	 */
	public const POST_TYPES = [ 'post', 'page' ];

	/**
	 * Whether the poll is available for a post type.
	 *
	 * @since 0.3.0
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	public static function supports_post_type( string $post_type ): bool {
		return in_array( $post_type, self::POST_TYPES, true );
	}

	/**
	 * Whether the post accepts votes.
	 *
	 * @since 0.3.0
	 *
	 * @param WP_Post|null $post Post, null when it does not exist.
	 * @return bool
	 */
	public function allows( ?WP_Post $post ): bool {
		return $post instanceof WP_Post
			&& self::supports_post_type( $post->post_type )
			&& '' === $post->post_password
			&& is_post_publicly_viewable( $post );
	}
}
