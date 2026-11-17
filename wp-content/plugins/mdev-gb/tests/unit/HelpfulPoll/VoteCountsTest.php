<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\VoteCounts and Mdev\Gb\HelpfulPoll\Vote.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Brain\Monkey\Functions;
use Mdev\Gb\HelpfulPoll\Vote;
use Mdev\Gb\HelpfulPoll\VoteCounts;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Meta registration, reading and incrementing counts.
 * Techniques: Brain Monkey expectations with argument matchers, and an
 * in-memory meta store behind get_post_meta / update_post_meta.
 */
final class VoteCountsTest extends HelpfulPollTestCase {

	/**
	 * Stored meta by "post_id:key".
	 *
	 * @var array<string, mixed>
	 */
	private array $meta = [];

	/**
	 * In-memory meta store.
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\stubs(
			[
				'get_post_meta' => fn ( int $id, string $key ): mixed => $this->meta[ $id . ':' . $key ] ?? 0,
			]
		);
	}

	/**
	 * Meta keys are protected (leading underscore) and per answer.
	 */
	public function test_vote_meta_keys(): void {
		self::assertSame( '_mdev_gb_helpful_yes', Vote::Yes->meta_key() );
		self::assertSame( '_mdev_gb_helpful_no', Vote::No->meta_key() );
		self::assertSame( [ 'yes', 'no' ], Vote::values() );
	}

	/**
	 * Each key: integer, single, sanitized, not editable, editors-only in REST.
	 */
	public function test_registers_meta_for_each_answer(): void {
		$counts = new VoteCounts();

		foreach ( [ '_mdev_gb_helpful_yes', '_mdev_gb_helpful_no' ] as $key ) {
			Functions\expect( 'register_post_meta' )
				->once()
				->with(
					'',
					$key,
					\Mockery::on(
						static fn ( array $args ): bool => 'integer' === $args['type']
							&& true === $args['single']
							&& 0 === $args['default']
							&& [ $counts, 'sanitize_count' ] === $args['sanitize_callback']
							&& [ $counts, 'can_edit' ] === $args['auth_callback']
							&& [ 'edit' ] === $args['show_in_rest']['schema']['context']
					)
				);
		}

		$counts->register_meta();
	}

	/**
	 * Nobody may edit counts through the meta APIs.
	 */
	public function test_counts_are_not_user_editable(): void {
		self::assertFalse( ( new VoteCounts() )->can_edit() );
	}

	/**
	 * Counts are non-negative integers.
	 *
	 * @param mixed $raw      Raw value.
	 * @param int   $expected Sanitized value.
	 */
	#[DataProvider( 'raw_counts' )]
	public function test_sanitize_count( mixed $raw, int $expected ): void {
		self::assertSame( $expected, ( new VoteCounts() )->sanitize_count( $raw ) );
	}

	/**
	 * Raw count cases.
	 *
	 * @return array<string, array{mixed, int}>
	 */
	public static function raw_counts(): array {
		return [
			'integer'        => [ 5, 5 ],
			'numeric string' => [ '12', 12 ],
			'negative'       => [ -3, 0 ],
			'empty string'   => [ '', 0 ],
			'text'           => [ 'lots', 0 ],
			'array'          => [ [ 1 ], 0 ],
		];
	}

	/**
	 * Both counts, as integers, defaulting to zero.
	 */
	public function test_get_reads_both_counts(): void {
		$this->meta['4:_mdev_gb_helpful_yes'] = '9';

		self::assertSame(
			[
				'yes' => 9,
				'no'  => 0,
			],
			( new VoteCounts() )->get( 4 )
		);
	}

	/**
	 * Increment adds one to the stored count of that answer only.
	 */
	public function test_increment_adds_one(): void {
		$this->meta['4:_mdev_gb_helpful_no'] = '2';

		Functions\expect( 'update_post_meta' )
			->once()
			->with( 4, '_mdev_gb_helpful_no', 3 );

		self::assertSame( 3, ( new VoteCounts() )->increment( 4, Vote::No ) );
	}
}
