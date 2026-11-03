<?php
/**
 * Covers Mdev\Gb\Blocks\Callout\Tone.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\Blocks\Callout;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mdev\Gb\Blocks\Callout\Tone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tone sanitization and labels.
 */
final class ToneTest extends TestCase {

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
	 * Every allowed tone passes through unchanged.
	 */
	public function test_allowed_tones_pass_through(): void {
		foreach ( Tone::ALLOWED as $tone ) {
			self::assertSame( $tone, Tone::sanitize( $tone ) );
		}
	}

	/**
	 * Case and whitespace are normalized.
	 */
	public function test_normalizes_case_and_whitespace(): void {
		self::assertSame( 'warning', Tone::sanitize( '  WARNING ' ) );
	}

	/**
	 * Invalid, hostile or non-string input falls back to the default.
	 *
	 * @param mixed $value Untrusted value.
	 */
	#[DataProvider( 'invalid_values' )]
	public function test_invalid_values_fall_back_to_default( mixed $value ): void {
		self::assertSame( Tone::DEFAULT, Tone::sanitize( $value ) );
	}

	/**
	 * Invalid tone values.
	 *
	 * @return array<string, array{0: mixed}>
	 */
	public static function invalid_values(): array {
		return [
			'unknown'      => [ 'purple' ],
			'empty'        => [ '' ],
			'markup'       => [ '"><script>alert(1)</script>' ],
			'class inject' => [ 'info is-hacked' ],
			'null'         => [ null ],
			'int'          => [ 3 ],
			'array'        => [ [ 'info' ] ],
		];
	}

	/**
	 * Each tone has a distinct label; invalid tones use the default label.
	 */
	public function test_labels(): void {
		self::assertSame( 'Information', Tone::label( 'info' ) );
		self::assertSame( 'Success', Tone::label( 'success' ) );
		self::assertSame( 'Warning', Tone::label( 'warning' ) );
		self::assertSame( 'Important', Tone::label( 'danger' ) );
		self::assertSame( 'Information', Tone::label( 'bogus' ) );
	}
}
