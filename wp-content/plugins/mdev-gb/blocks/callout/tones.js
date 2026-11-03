/**
 * Tone values for the callout block.
 * Keep in sync with block.json and src/Blocks/Callout/Tone.php.
 */
import { __ } from '@wordpress/i18n';

export const DEFAULT_TONE = 'info';
export const TONES = [ 'info', 'success', 'warning', 'danger' ];

/**
 * Normalize an arbitrary value to an allowed tone.
 *
 * @param {unknown} value Raw value.
 * @return {string} An allowed tone.
 */
export function normalizeTone( value ) {
	return TONES.includes( value ) ? value : DEFAULT_TONE;
}

/**
 * Options for the tone SelectControl.
 *
 * @return {Array<{label: string, value: string}>} Options.
 */
export function getToneOptions() {
	return [
		{ label: __( 'Information', 'mdev-gb' ), value: 'info' },
		{ label: __( 'Success', 'mdev-gb' ), value: 'success' },
		{ label: __( 'Warning', 'mdev-gb' ), value: 'warning' },
		{ label: __( 'Important', 'mdev-gb' ), value: 'danger' },
	];
}
