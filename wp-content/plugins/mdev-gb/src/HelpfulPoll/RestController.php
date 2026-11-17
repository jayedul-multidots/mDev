<?php
/**
 * REST endpoint recording helpful poll votes.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * `POST /wp-json/mdev-gb/v1/helpful-poll/<id>` with `{"vote": "yes"|"no"}`.
 *
 * Anonymous by design: any visitor of a public post may vote, so there is no
 * capability to check and no nonce (a logged-out nonce is the same for every
 * visitor, and it would go stale in full-page-cached HTML). Authorization is
 * instead "the post is publicly readable" (VotablePolicy); the vote is an
 * allowlisted enum and the ID a positive integer, both enforced by the schema.
 * The response carries no counts. Repeat votes are limited in the browser
 * (localStorage), so the counts are feedback, not tamper-proof figures.
 *
 * @since 0.3.0
 */
final class RestController {

	public const NAMESPACE = 'mdev-gb/v1';
	public const ROUTE     = '/helpful-poll';

	/**
	 * Constructor.
	 *
	 * @since 0.3.0
	 *
	 * @param VoteCounts    $counts Count storage.
	 * @param VotablePolicy $policy Which posts accept votes.
	 */
	public function __construct(
		private readonly VoteCounts $counts,
		private readonly VotablePolicy $policy,
	) {
	}

	/**
	 * Vote endpoint URL for a post.
	 *
	 * @since 0.3.0
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function url( int $post_id ): string {
		return rest_url( self::NAMESPACE . self::ROUTE . '/' . $post_id );
	}

	/**
	 * Register the route. Hooked to `rest_api_init`.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE . '/(?P<id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'permissions_check' ],
				'args'                => $this->get_args(),
			]
		);
	}

	/**
	 * Votes are accepted only for publicly readable posts. Others get a 404,
	 * so the endpoint reveals nothing about drafts or private posts.
	 *
	 * @since 0.3.0
	 *
	 * @param WP_REST_Request $request Request.
	 * @return true|WP_Error
	 *
	 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
	 */
	public function permissions_check( WP_REST_Request $request ): bool|WP_Error {
		$post = get_post( absint( $request->get_param( 'id' ) ) );

		if ( $this->policy->allows( $post ) ) {
			return true;
		}

		return new WP_Error(
			'mdev_gb_poll_not_found',
			__( 'This post does not accept feedback.', 'mdev-gb' ),
			[ 'status' => 404 ]
		);
	}

	/**
	 * Record the vote.
	 *
	 * @since 0.3.0
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 *
	 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
	 */
	public function create_item( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$raw_vote = $request->get_param( 'vote' );
		$vote     = is_string( $raw_vote ) ? Vote::tryFrom( $raw_vote ) : null;

		if ( null === $vote ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'Invalid vote.', 'mdev-gb' ),
				[ 'status' => 400 ]
			);
		}

		$this->counts->increment( absint( $request->get_param( 'id' ) ), $vote );

		return new WP_REST_Response( [ 'recorded' => true ], 201 );
	}

	/**
	 * Argument schema, enforced by WordPress before the callbacks run.
	 *
	 * @since 0.3.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_args(): array {
		return [
			'id'   => [
				'type'     => 'integer',
				'minimum'  => 1,
				'required' => true,
			],
			'vote' => [
				'type'     => 'string',
				'enum'     => Vote::values(),
				'required' => true,
			],
		];
	}
}
