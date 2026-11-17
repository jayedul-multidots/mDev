<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\RestController.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Brain\Monkey\Functions;
use Mdev\Gb\HelpfulPoll\RestController;
use Mdev\Gb\HelpfulPoll\VotablePolicy;
use Mdev\Gb\HelpfulPoll\Vote;
use Mdev\Gb\HelpfulPoll\VoteCounts;
use WP_Error;
use WP_REST_Request;

/**
 * Route registration, the "public post only" permission check, and
 * recording votes (including hostile input that bypassed the schema).
 */
final class RestControllerTest extends HelpfulPollTestCase {

	/**
	 * Controller over the real count store; tests set expectations on the
	 * meta functions underneath it.
	 *
	 * @return RestController
	 */
	private function controller(): RestController {
		Functions\when( 'get_post_meta' )->justReturn( '4' );
		return new RestController( new VoteCounts(), new VotablePolicy() );
	}

	/**
	 * POST route with an ID in the path and the vote enum.
	 */
	public function test_registers_route(): void {
		$controller = $this->controller();

		Functions\expect( 'register_rest_route' )
			->once()
			->with(
				'mdev-gb/v1',
				'/helpful-poll/(?P<id>\d+)',
				\Mockery::on(
					static fn ( array $route ): bool => 'POST' === $route['methods']
						&& [ $controller, 'permissions_check' ] === $route['permission_callback']
						&& [ $controller, 'create_item' ] === $route['callback']
						&& [ 'yes', 'no' ] === $route['args']['vote']['enum']
						&& true === $route['args']['vote']['required']
						&& 1 === $route['args']['id']['minimum']
				)
			);

		$controller->register_routes();
	}

	/**
	 * Anyone may vote on a public post.
	 */
	public function test_permission_granted_for_public_post(): void {
		$this->add_post( 5 );

		self::assertTrue( $this->controller()->permissions_check( new WP_REST_Request( [ 'id' => 5 ] ) ) );
	}

	/**
	 * Missing and unreadable posts look the same: 404.
	 *
	 * @return void
	 */
	public function test_permission_denied_with_404_for_unreadable_posts(): void {
		$this->add_post( 5, [ 'post_status' => 'draft' ] );

		foreach ( [ 5, 999 ] as $id ) {
			$result = $this->controller()->permissions_check( new WP_REST_Request( [ 'id' => $id ] ) );

			self::assertInstanceOf( WP_Error::class, $result );
			self::assertSame( 'mdev_gb_poll_not_found', $result->get_error_code() );
			self::assertSame( [ 'status' => 404 ], $result->get_error_data() );
		}
	}

	/**
	 * A valid vote is counted and acknowledged without revealing counts.
	 */
	public function test_create_item_records_vote(): void {
		Functions\expect( 'update_post_meta' )
			->once()
			->with( 5, Vote::Yes->meta_key(), 5 );

		$response = $this->controller()->create_item(
			new WP_REST_Request(
				[
					'id'   => '5',
					'vote' => 'yes',
				]
			)
		);

		self::assertNotInstanceOf( WP_Error::class, $response );
		self::assertSame( 201, $response->get_status() );
		self::assertSame( [ 'recorded' => true ], $response->get_data() );
	}

	/**
	 * Values outside the enum never reach storage, even if schema validation
	 * were bypassed.
	 *
	 * @return void
	 */
	public function test_create_item_rejects_invalid_vote(): void {
		Functions\expect( 'update_post_meta' )->never();

		foreach ( [ 'maybe', 'YES', [ 'yes' ], null ] as $vote ) {
			$response = $this->controller()->create_item(
				new WP_REST_Request(
					[
						'id'   => 5,
						'vote' => $vote,
					]
				)
			);

			self::assertInstanceOf( WP_Error::class, $response );
			self::assertSame( [ 'status' => 400 ], $response->get_error_data() );
		}
	}

	/**
	 * Endpoint URL helper.
	 */
	public function test_url(): void {
		self::assertSame( 'https://example.test/wp-json/mdev-gb/v1/helpful-poll/12', RestController::url( 12 ) );
	}
}
