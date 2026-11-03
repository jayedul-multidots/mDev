<?php
/**
 * Covers Mdev\Gb\Blocks\BlockCategory.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mdev\Gb\Blocks\BlockCategory;
use PHPUnit\Framework\TestCase;

/**
 * The mdev category is prepended exactly once.
 */
final class BlockCategoryTest extends TestCase {

	/**
	 * Set up Brain Monkey.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Functions\stubTranslationFunctions();
	}

	/**
	 * Tear down Brain Monkey.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * The category is added first and existing categories are preserved.
	 */
	public function test_prepends_category(): void {
		$existing = [
			[
				'slug'  => 'text',
				'title' => 'Text',
			],
		];

		$result = ( new BlockCategory() )->register( $existing );

		self::assertCount( 2, $result );
		self::assertSame( BlockCategory::SLUG, $result[0]['slug'] );
		self::assertSame( 'text', $result[1]['slug'] );
	}

	/**
	 * Running the filter twice never duplicates the category.
	 */
	public function test_does_not_duplicate_category(): void {
		$category = new BlockCategory();
		$once     = $category->register( [] );

		self::assertSame( $once, $category->register( $once ) );
	}

	/**
	 * Malformed category entries without a slug are tolerated.
	 */
	public function test_tolerates_entries_without_slug(): void {
		$result = ( new BlockCategory() )->register( [ [ 'title' => 'Broken' ] ] );

		self::assertSame( BlockCategory::SLUG, $result[0]['slug'] );
		self::assertCount( 2, $result );
	}
}
