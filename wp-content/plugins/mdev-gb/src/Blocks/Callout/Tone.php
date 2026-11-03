<?php
/**
 * Tone values for the callout block.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\Blocks\Callout;

/**
 * Validates and labels the `tone` attribute of `mdev-gb/callout`.
 *
 * Keep ALLOWED in sync with `blocks/callout/block.json` and `tones.js`.
 *
 * @since 0.1.0
 */
final class Tone {

	/**
	 * Fallback tone.
	 *
	 * @since 0.1.0
	 */
	public const DEFAULT = 'info';

	/**
	 * Accepted tone values.
	 *
	 * @since 0.1.0
	 */
	public const ALLOWED = [ 'info', 'success', 'warning', 'danger' ];

	/**
	 * Normalize an untrusted attribute value to an allowed tone.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Raw attribute value.
	 * @return string One of self::ALLOWED.
	 */
	public static function sanitize( mixed $value ): string {
		if ( ! is_string( $value ) ) {
			return self::DEFAULT;
		}

		$value = strtolower( trim( $value ) );

		return in_array( $value, self::ALLOWED, true ) ? $value : self::DEFAULT;
	}

	/**
	 * Accessible, translated label for a tone.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tone Tone value (sanitized internally).
	 * @return string Human readable label.
	 */
	public static function label( string $tone ): string {
		return match ( self::sanitize( $tone ) ) {
			'success' => __( 'Success', 'mdev-gb' ),
			'warning' => __( 'Warning', 'mdev-gb' ),
			'danger'  => __( 'Important', 'mdev-gb' ),
			default   => __( 'Information', 'mdev-gb' ),
		};
	}
}
