/**
 * Gallery state in the page URL. Mirrors src/PostGallery/UrlParams.php so
 * that URLs produced in the browser and on the server are interchangeable.
 */
import { DEFAULT_SORT, normalizeSort } from './sort-values';

export const PARAMS = Object.freeze( {
	search: 'q',
	sort: 'sort',
	page: 'page',
} );

/**
 * Read a gallery's state from a URL.
 *
 * @param {string} href   Absolute URL.
 * @param {string} prefix Instance prefix, e.g. "pg1_".
 * @return {{search?: string, sort?: string, page?: number}} Present values only.
 */
export function readUrlState( href, prefix ) {
	const params = new URL( href ).searchParams;
	const state = {};

	if ( params.has( prefix + PARAMS.search ) ) {
		state.search = params.get( prefix + PARAMS.search );
	}
	if ( params.has( prefix + PARAMS.sort ) ) {
		state.sort = params.get( prefix + PARAMS.sort );
	}
	if ( params.has( prefix + PARAMS.page ) ) {
		const page = Number.parseInt( params.get( prefix + PARAMS.page ), 10 );
		state.page = Number.isFinite( page ) && page > 0 ? page : 1;
	}

	return state;
}

/**
 * URL for a gallery state. Other parameters (and other galleries' state) are
 * kept; defaults are omitted so URLs stay short and canonical.
 *
 * @param {string} href           Current absolute URL.
 * @param {string} prefix         Instance prefix.
 * @param {Object} state          Desired state.
 * @param {string} [state.search] Search term.
 * @param {string} [state.sort]   Sort value.
 * @param {number} [state.page]   Page number.
 * @param {string} defaultSort    The gallery's default sort.
 * @return {string} Absolute URL.
 */
export function buildUrl(
	href,
	prefix,
	{ search = '', sort, page = 1 },
	defaultSort = DEFAULT_SORT
) {
	const url = new URL( href );
	const params = url.searchParams;
	const term = search.trim();
	const sortValue = normalizeSort( sort, defaultSort );

	Object.values( PARAMS ).forEach( ( key ) => params.delete( prefix + key ) );

	if ( term !== '' ) {
		params.set( prefix + PARAMS.search, term );
	}
	if ( sortValue !== defaultSort ) {
		params.set( prefix + PARAMS.sort, sortValue );
	}
	if ( page > 1 ) {
		params.set( prefix + PARAMS.page, String( page ) );
	}

	return url.toString();
}
