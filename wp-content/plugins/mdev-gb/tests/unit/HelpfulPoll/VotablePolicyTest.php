<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\VotablePolicy.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Mdev\Gb\HelpfulPoll\VotablePolicy;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Only posts anonymous visitors can read accept votes.
 */
final class VotablePolicyTest extends HelpfulPollTestCase {

	/**
	 * Published posts and pages accept votes.
	 */
	public function test_allows_published_posts_and_pages(): void {
		self::assertTrue( ( new VotablePolicy() )->allows( $this->add_post( 1 ) ) );
		self::assertTrue( ( new VotablePolicy() )->allows( $this->add_post( 2, [ 'post_type' => 'page' ] ) ) );
	}

	/**
	 * Only posts and pages support the poll.
	 */
	public function test_supported_post_types(): void {
		self::assertTrue( VotablePolicy::supports_post_type( 'post' ) );
		self::assertTrue( VotablePolicy::supports_post_type( 'page' ) );
		self::assertFalse( VotablePolicy::supports_post_type( 'deep_pockets_videos' ) );
		self::assertFalse( VotablePolicy::supports_post_type( '' ) );
	}

	/**
	 * A missing post never does.
	 */
	public function test_rejects_missing_post(): void {
		self::assertFalse( ( new VotablePolicy() )->allows( null ) );
	}

	/**
	 * Posts visitors cannot read, and custom post types, never accept votes.
	 *
	 * @param array<string, string> $props Post properties.
	 */
	#[DataProvider( 'unreadable_posts' )]
	public function test_rejects_posts_visitors_cannot_read( array $props ): void {
		self::assertFalse( ( new VotablePolicy() )->allows( $this->add_post( 1, $props ) ) );
	}

	/**
	 * Unreadable post cases.
	 *
	 * @return array<string, array{array<string, string>}>
	 */
	public static function unreadable_posts(): array {
		return [
			'draft'              => [ [ 'post_status' => 'draft' ] ],
			'private'            => [ [ 'post_status' => 'private' ] ],
			'password protected' => [ [ 'post_password' => 'secret' ] ],
			'non-public type'    => [ [ 'post_type' => 'wp_block' ] ],
			'public custom type' => [ [ 'post_type' => 'deep_pockets_videos' ] ],
		];
	}
}
