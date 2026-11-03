/**
 * Translated sort options for the block editor.
 */
import { __ } from '@wordpress/i18n';

import { SORT_VALUES } from './sort-values';

/**
 * Options for a SelectControl, in SORT_VALUES order.
 *
 * @return {Array<{label: string, value: string}>} Options.
 */
export function getSortOptions() {
	const labels = {
		relevance: __( 'Best match', 'mdev-gb' ),
		newest: __( 'Newest first', 'mdev-gb' ),
		oldest: __( 'Oldest first', 'mdev-gb' ),
		'title-asc': __( 'Title (A–Z)', 'mdev-gb' ),
		'title-desc': __( 'Title (Z–A)', 'mdev-gb' ),
		'most-discussed': __( 'Most discussed', 'mdev-gb' ),
	};

	return SORT_VALUES.map( ( value ) => ( {
		value,
		label: labels[ value ],
	} ) );
}
