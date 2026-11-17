/**
 * Reads the vote counts from a post's meta in the editor.
 * Keys: src/HelpfulPoll/Vote.php (META_PREFIX + vote).
 */
export const META_KEYS = Object.freeze( {
	yes: '_mdev_gb_helpful_yes',
	no: '_mdev_gb_helpful_no',
} );

const toCount = ( value ) =>
	Number.isFinite( value ) && value >= 0 ? Math.trunc( value ) : 0;

/**
 * Counts from post meta.
 *
 * @param {unknown} meta Post meta object (edit context).
 * @return {{yes: number, no: number}|null} Counts, or null when the meta is
 *                                         not loaded or not exposed.
 */
export function readCounts( meta ) {
	if (
		! meta ||
		typeof meta !== 'object' ||
		! ( META_KEYS.yes in meta ) ||
		! ( META_KEYS.no in meta )
	) {
		return null;
	}
	return {
		yes: toCount( meta[ META_KEYS.yes ] ),
		no: toCount( meta[ META_KEYS.no ] ),
	};
}
