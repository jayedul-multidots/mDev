<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\EditorAvailability.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Mdev\Gb\HelpfulPoll\EditorAvailability;
use PHPUnit\Framework\Attributes\DataProvider;
use WP_Block_Type;
use WP_Block_Type_Registry;
use WP_Screen;

/**
 * The block leaves the inserter only in editors of other post types.
 */
final class EditorAvailabilityTest extends HelpfulPollTestCase {

	/**
	 * Registry holding the poll block (with its block.json supports).
	 *
	 * @var WP_Block_Type_Registry
	 */
	private WP_Block_Type_Registry $registry;

	/**
	 * The poll block type.
	 *
	 * @var WP_Block_Type
	 */
	private WP_Block_Type $block;

	/**
	 * Fresh registry per test.
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->block    = new WP_Block_Type( 'mdev-gb/helpful-poll', [ 'supports' => [ 'multiple' => false ] ] );
		$this->registry = new WP_Block_Type_Registry();
		$this->registry->register( $this->block );
	}

	/**
	 * Editing a custom post type: removed from the inserter, other supports kept.
	 */
	public function test_hidden_in_custom_post_type_editor(): void {
		( new EditorAvailability( $this->registry ) )->restrict(
			new WP_Screen(
				[
					'base'      => 'post',
					'post_type' => 'deep_pockets_videos',
				]
			)
		);

		self::assertSame(
			[
				'multiple' => false,
				'inserter' => false,
			],
			$this->block->supports
		);
	}

	/**
	 * Post and page editors, and non-editor screens (site editor, lists),
	 * are left alone.
	 *
	 * @param string $base      Screen base.
	 * @param string $post_type Screen post type.
	 */
	#[DataProvider( 'untouched_screens' )]
	public function test_untouched_elsewhere( string $base, string $post_type ): void {
		( new EditorAvailability( $this->registry ) )->restrict(
			new WP_Screen(
				[
					'base'      => $base,
					'post_type' => $post_type,
				]
			)
		);

		self::assertSame( [ 'multiple' => false ], $this->block->supports );
	}

	/**
	 * Untouched screen cases.
	 *
	 * @return array<string, array{string, string}>
	 */
	public static function untouched_screens(): array {
		return [
			'post editor'      => [ 'post', 'post' ],
			'page editor'      => [ 'post', 'page' ],
			'site editor'      => [ 'site-editor', '' ],
			'custom type list' => [ 'edit', 'deep_pockets_videos' ],
		];
	}

	/**
	 * Before the block is built (no manifest), nothing happens.
	 */
	public function test_unregistered_block_is_ignored(): void {
		( new EditorAvailability( new WP_Block_Type_Registry() ) )->restrict(
			new WP_Screen(
				[
					'base'      => 'post',
					'post_type' => 'deep_pockets_videos',
				]
			)
		);

		self::assertSame( [ 'multiple' => false ], $this->block->supports );
	}
}
