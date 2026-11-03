/**
 * Sort values shared by the editor and the front end.
 *
 * No `@wordpress/i18n` import here: this module is bundled into the front-end
 * script module, where wp-i18n is not available. Labels live in sort-options.js.
 *
 * Keep in sync with src/PostGallery/SortOption.php and block.json.
 */

export const SORT_VALUES = Object.freeze( [
	'relevance',
	'newest',
	'oldest',
	'title-asc',
	'title-desc',
	'most-discussed',
] );

export const DEFAULT_SORT = 'newest';

/**
 * Return `value` when it is an allowed sort, otherwise `fallback`.
 *
 * @param {unknown} value    Raw value.
 * @param {string}  fallback Fallback sort.
 * @return {string} Allowed sort value.
 */
export function normalizeSort( value, fallback = DEFAULT_SORT ) {
	if ( typeof value !== 'string' ) {
		return fallback;
	}
	const sort = value.trim().toLowerCase();
	return SORT_VALUES.includes( sort ) ? sort : fallback;
}
