<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\AdminColumns.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Brain\Monkey\Functions;
use Mdev\Gb\HelpfulPoll\AdminColumns;
use Mdev\Gb\HelpfulPoll\VoteCounts;

/**
 * Column placement and cell output (permission, empty and counted states).
 * Techniques: an in-memory meta store behind get_post_meta, and a
 * current_user_can stub driven by a per-test allowlist.
 */
final class AdminColumnsTest extends HelpfulPollTestCase {

	/**
	 * Stored meta by "post_id:key".
	 *
	 * @var array<string, mixed>
	 */
	private array $meta = [];

	/**
	 * Posts the current user may edit.
	 *
	 * @var list<int>
	 */
	private array $editable = [ 1 ];

	/**
	 * Meta store and capability stubs.
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\stubs(
			[
				'get_post_meta'      => fn ( int $id, string $key ): mixed => $this->meta[ $id . ':' . $key ] ?? 0,
				'current_user_can'   => fn ( string $cap, int $id ): bool => 'edit_post' === $cap && in_array( $id, $this->editable, true ),
				'number_format_i18n' => static fn ( int $number ): string => number_format( $number ),
			]
		);
	}

	/**
	 * Columns under test.
	 *
	 * @return AdminColumns
	 */
	private function columns(): AdminColumns {
		return new AdminColumns( new VoteCounts() );
	}

	/**
	 * The column goes right before the date.
	 */
	public function test_add_column_before_date(): void {
		$columns = $this->columns()->add_column(
			[
				'cb'    => '<input type="checkbox" />',
				'title' => 'Title',
				'date'  => 'Date',
			]
		);

		self::assertSame( [ 'cb', 'title', 'mdev_gb_helpful', 'date' ], array_keys( $columns ) );
		self::assertSame( 'Helpful', $columns['mdev_gb_helpful'] );
	}

	/**
	 * Without a date column it goes last.
	 */
	public function test_add_column_without_date(): void {
		self::assertSame(
			[ 'title', 'mdev_gb_helpful' ],
			array_keys( $this->columns()->add_column( [ 'title' => 'Title' ] ) )
		);
	}

	/**
	 * Counts are shown, formatted, on separate lines.
	 */
	public function test_cell_shows_counts(): void {
		$this->meta['1:_mdev_gb_helpful_yes'] = '1200';
		$this->meta['1:_mdev_gb_helpful_no']  = '3';

		$cell = $this->columns()->cell( 1 );

		self::assertSame(
			'<span class="mdev-gb-helpful-yes">Yes: 1,200</span><br><span class="mdev-gb-helpful-no">No: 3</span>',
			$cell
		);
	}

	/**
	 * No votes: an em dash with screen reader text.
	 */
	public function test_cell_without_votes(): void {
		$cell = $this->columns()->cell( 1 );

		self::assertStringContainsString( '&#8212;', $cell );
		self::assertStringContainsString( 'No votes yet', $cell );
	}

	/**
	 * Users who cannot edit the post do not see its counts.
	 */
	public function test_cell_hides_counts_from_users_who_cannot_edit(): void {
		$this->meta['2:_mdev_gb_helpful_yes'] = '7';

		$cell = $this->columns()->cell( 2 );

		self::assertStringNotContainsString( '7', $cell );
		self::assertStringContainsString( 'Not available', $cell );
	}

	/**
	 * Only our column prints anything.
	 */
	public function test_render_column_prints_only_its_own_column(): void {
		$this->meta['1:_mdev_gb_helpful_yes'] = '5';

		ob_start();
		$this->columns()->render_column( 'author', 1 );
		$this->columns()->render_column( 'mdev_gb_helpful', 1 );
		$output = (string) ob_get_clean();

		self::assertStringStartsWith( '<span class="mdev-gb-helpful-yes">Yes: 5</span>', $output );
	}
}
