<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\PollView.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Brain\Monkey\Functions;
use Mdev\Gb\HelpfulPoll\PollView;
use Mdev\Gb\HelpfulPoll\VotablePolicy;

/**
 * Which post the poll is for, when it renders, its text and its context.
 */
final class PollViewTest extends HelpfulPollTestCase {

	/**
	 * View under test.
	 *
	 * @return PollView
	 */
	private function view(): PollView {
		return new PollView( new VotablePolicy() );
	}

	/**
	 * Block context wins (post content, Query Loop).
	 */
	public function test_resolve_post_id_from_context(): void {
		Functions\expect( 'is_singular' )->never();

		self::assertSame( 7, $this->view()->resolve_post_id( [ 'postId' => 7 ] ) );
		self::assertSame( 8, $this->view()->resolve_post_id( [ 'postId' => '8' ] ) );
	}

	/**
	 * Without context (block templates), the queried post on singular views.
	 */
	public function test_resolve_post_id_falls_back_to_queried_post(): void {
		Functions\when( 'is_singular' )->justReturn( true );
		Functions\when( 'get_queried_object_id' )->justReturn( 9 );

		self::assertSame( 9, $this->view()->resolve_post_id( [ 'postId' => 'nope' ] ) );
	}

	/**
	 * No post at all on archives and other non-singular views.
	 */
	public function test_resolve_post_id_is_zero_elsewhere(): void {
		Functions\when( 'is_singular' )->justReturn( false );

		self::assertSame( 0, $this->view()->resolve_post_id( [] ) );
	}

	/**
	 * Nothing renders without a post that accepts votes.
	 */
	public function test_build_is_null_without_votable_post(): void {
		$this->add_post( 3, [ 'post_status' => 'draft' ] );

		self::assertNull( $this->view()->build( [], 0 ) );
		self::assertNull( $this->view()->build( [], 3 ) );
		self::assertNull( $this->view()->build( [], 404 ) );
	}

	/**
	 * Empty attributes use the translated defaults; the context is the
	 * initial, not-yet-voted state for every visitor (cache safe).
	 */
	public function test_build_defaults(): void {
		$this->add_post( 3 );

		$view = $this->view()->build( [], 3 );

		self::assertNotNull( $view );
		self::assertSame( 'Was this article helpful?', $view['question'] );
		self::assertSame( 'Yes', $view['yes'] );
		self::assertSame( 'No', $view['no'] );
		self::assertSame(
			[
				'postId'       => 3,
				'endpoint'     => 'https://example.test/wp-json/mdev-gb/v1/helpful-poll/3',
				'hasVoted'     => false,
				'isSubmitting' => false,
				'message'      => '',
				'error'        => '',
				'thanks'       => 'Thanks for your feedback!',
				'errorMessage' => 'Your feedback could not be sent. Please try again.',
			],
			$view['context']
		);
	}

	/**
	 * Custom text is passed through raw (render.php escapes it); markup-only
	 * or non-string values fall back to the defaults.
	 */
	public function test_build_custom_text(): void {
		$this->add_post( 3 );

		$view = $this->view()->build(
			[
				'question'      => 'Did this <strong>help</strong>?',
				'yesLabel'      => 'Yep',
				'noLabel'       => '<br>',
				'thanksMessage' => [ 'not', 'text' ],
			],
			3
		);

		self::assertNotNull( $view );
		self::assertSame( 'Did this <strong>help</strong>?', $view['question'] );
		self::assertSame( 'Yep', $view['yes'] );
		self::assertSame( 'No', $view['no'] );
		self::assertSame( 'Thanks for your feedback!', $view['context']['thanks'] );
	}
}
