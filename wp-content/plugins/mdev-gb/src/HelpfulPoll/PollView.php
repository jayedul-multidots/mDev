<?php
/**
 * View data for the helpful poll block.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

/**
 * Turns block attributes and the current post into what render.php prints
 * and the Interactivity API context the browser store starts from.
 *
 * Holds no per-visitor state: the markup is the same for everyone, so it is
 * safe behind a full-page cache. Whether this visitor already voted is read
 * from localStorage in the browser.
 *
 * @since 0.3.0
 */
final class PollView {

	public const STORE = 'mdev-gb/helpful-poll';

	/**
	 * Constructor.
	 *
	 * @since 0.3.0
	 *
	 * @param VotablePolicy $policy Which posts accept votes.
	 */
	public function __construct( private readonly VotablePolicy $policy ) {
	}

	/**
	 * The post the poll is about: the block context's post (post content,
	 * Query Loop), else the queried post on singular views (block templates).
	 *
	 * @since 0.3.0
	 *
	 * @param array<string, mixed> $context Block context.
	 * @return int Post ID, 0 when there is none.
	 */
	public function resolve_post_id( array $context ): int {
		$post_id = $context['postId'] ?? null;

		if ( is_int( $post_id ) || ( is_string( $post_id ) && ctype_digit( $post_id ) ) ) {
			return absint( $post_id );
		}

		return is_singular() ? get_queried_object_id() : 0;
	}

	/**
	 * View data, or null when the poll must not render (no post, or a post
	 * that does not accept votes).
	 *
	 * Text values are raw: render.php escapes them on output.
	 *
	 * @since 0.3.0
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param int                  $post_id    Post ID.
	 * @return array{question: string, yes: string, no: string, context: array<string, mixed>}|null
	 */
	public function build( array $attributes, int $post_id ): ?array {
		if ( $post_id < 1 || ! $this->policy->allows( get_post( $post_id ) ) ) {
			return null;
		}

		return [
			'question' => $this->text( $attributes, 'question', __( 'Was this article helpful?', 'mdev-gb' ) ),
			'yes'      => $this->text( $attributes, 'yesLabel', __( 'Yes', 'mdev-gb' ) ),
			'no'       => $this->text( $attributes, 'noLabel', __( 'No', 'mdev-gb' ) ),
			'context'  => [
				'postId'       => $post_id,
				'endpoint'     => esc_url_raw( RestController::url( $post_id ) ),
				'hasVoted'     => false,
				'isSubmitting' => false,
				'message'      => '',
				'error'        => '',
				'thanks'       => $this->text( $attributes, 'thanksMessage', __( 'Thanks for your feedback!', 'mdev-gb' ) ),
				'errorMessage' => __( 'Your feedback could not be sent. Please try again.', 'mdev-gb' ),
			],
		];
	}

	/**
	 * A text attribute, or the translated default when it is empty.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $key        Attribute name.
	 * @param string               $fallback   Default text.
	 * @return string
	 */
	private function text( array $attributes, string $key, string $fallback ): string {
		$value = $attributes[ $key ] ?? '';

		if ( ! is_string( $value ) || '' === trim( wp_strip_all_tags( $value ) ) ) {
			return $fallback;
		}

		return $value;
	}
}
