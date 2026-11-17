<?php
/**
 * Helpful poll counts stored in post meta.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

/**
 * Reads and increments the per-post "yes" / "no" counts.
 *
 * The counts are registered post meta (one integer per answer) so the block
 * editor can show them. They are exposed in the REST API in the `edit`
 * context only (editors, not visitors) and cannot be written through it:
 * the vote endpoint is the only writer.
 *
 * @since 0.3.0
 */
final class VoteCounts {

	/**
	 * Register the meta keys for the post types the poll is available for.
	 * Hooked to `init`.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public function register_meta(): void {
		foreach ( VotablePolicy::POST_TYPES as $post_type ) {
			foreach ( Vote::cases() as $vote ) {
				$this->register_key( $post_type, $vote );
			}
		}
	}

	/**
	 * Register one count's meta key for one post type.
	 *
	 * @param string $post_type Post type.
	 * @param Vote   $vote      Answer.
	 * @return void
	 */
	private function register_key( string $post_type, Vote $vote ): void {
		register_post_meta(
			$post_type,
			$vote->meta_key(),
			[
				'type'              => 'integer',
				'description'       => sprintf( 'Number of "%s" answers to the helpful poll.', $vote->value ),
				'single'            => true,
				'default'           => 0,
				'sanitize_callback' => [ $this, 'sanitize_count' ],
				'auth_callback'     => [ $this, 'can_edit' ],
				'show_in_rest'      => [
					'schema' => [
						'type'    => 'integer',
						'minimum' => 0,
						'context' => [ 'edit' ],
					],
				],
			]
		);
	}

	/**
	 * Counts are never negative and always integers.
	 *
	 * @since 0.3.0
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public function sanitize_count( mixed $value ): int {
		return is_numeric( $value ) ? max( 0, (int) $value ) : 0;
	}

	/**
	 * Nobody edits the counts through the meta APIs exposed to users (REST,
	 * custom fields): they only change through a recorded vote.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	public function can_edit(): bool {
		return false;
	}

	/**
	 * Current counts for a post.
	 *
	 * @since 0.3.0
	 *
	 * @param int $post_id Post ID.
	 * @return array{yes: int, no: int}
	 */
	public function get( int $post_id ): array {
		return [
			Vote::Yes->value => $this->count( $post_id, Vote::Yes ),
			Vote::No->value  => $this->count( $post_id, Vote::No ),
		];
	}

	/**
	 * Add one vote.
	 *
	 * Read-then-write through the meta API (object cache aware). Two votes
	 * landing in the same instant may count as one; acceptable for a
	 * feedback widget, and it keeps the code on core APIs.
	 *
	 * @since 0.3.0
	 *
	 * @param int  $post_id Post ID.
	 * @param Vote $vote    Answer.
	 * @return int The new count.
	 */
	public function increment( int $post_id, Vote $vote ): int {
		$count = $this->count( $post_id, $vote ) + 1;

		update_post_meta( $post_id, $vote->meta_key(), $count );

		return $count;
	}

	/**
	 * One answer's count.
	 *
	 * @param int  $post_id Post ID.
	 * @param Vote $vote    Answer.
	 * @return int
	 */
	private function count( int $post_id, Vote $vote ): int {
		return $this->sanitize_count( get_post_meta( $post_id, $vote->meta_key(), true ) );
	}
}
