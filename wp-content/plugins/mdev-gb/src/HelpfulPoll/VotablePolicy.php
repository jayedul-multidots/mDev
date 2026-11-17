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
 * A post accepts votes when anonymous visitors can read it: published, of a
 * publicly viewable post type, and not password protected. Drafts, private
 * posts, previews and non-public post types never collect votes.
 *
 * @since 0.3.0
 */
final class VotablePolicy {

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
			&& '' === $post->post_password
			&& is_post_publicly_viewable( $post );
	}
}
