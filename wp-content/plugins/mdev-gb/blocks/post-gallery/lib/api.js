/**
 * REST client for GET mdev-gb/v1/post-gallery.
 */
import { DEFAULT_SORT, normalizeSort } from './sort-values';

/**
 * Error for failed or malformed responses.
 */
export class GalleryRequestError extends Error {
	/**
	 * @param {string} message Message (for logs; never shown to visitors).
	 * @param {number} status  HTTP status, 0 for network/parse errors.
	 */
	constructor( message, status = 0 ) {
		super( message );
		this.name = 'GalleryRequestError';
		this.status = status;
	}
}

/**
 * Build the request URL. Works with pretty (`/wp-json/...`) and plain
 * (`/?rest_route=...`) permalinks because the URL API merges parameters.
 *
 * @param {string} restUrl           Endpoint URL from the server.
 * @param {Object} query             Query.
 * @param {string} query.search      Search term.
 * @param {string} query.sort        Sort value.
 * @param {number} query.page        Page number.
 * @param {string} query.defaultSort Gallery default sort.
 * @param {Object} query.settings    Gallery settings from the server.
 * @return {string} Request URL.
 */
export function buildRequestUrl(
	restUrl,
	{ search = '', sort, page = 1, defaultSort = DEFAULT_SORT, settings = {} }
) {
	const url = new URL( restUrl );
	const params = url.searchParams;
	const term = search.trim();
	const categories = Array.isArray( settings.categories )
		? settings.categories
		: [];

	if ( term !== '' ) {
		params.set( 'search', term );
	}
	params.set( 'sort', normalizeSort( sort, defaultSort ) );
	params.set( 'default_sort', normalizeSort( defaultSort ) );
	params.set( 'page', String( Math.max( 1, Math.trunc( page ) || 1 ) ) );

	if ( settings.postType ) {
		params.set( 'post_type', settings.postType );
	}
	if ( settings.perPage ) {
		params.set( 'per_page', String( settings.perPage ) );
	}
	if ( settings.imageSize ) {
		params.set( 'image_size', settings.imageSize );
	}
	if ( categories.length > 0 ) {
		params.set( 'categories', categories.join( ',' ) );
	}

	return url.toString();
}

const toText = ( value ) => ( typeof value === 'string' ? value : '' );
const toCount = ( value ) =>
	Number.isFinite( value ) && value >= 0 ? Math.trunc( value ) : 0;

/**
 * Keep only well-formed image data.
 *
 * @param {unknown} image Raw image.
 * @return {Object|null} Image or null.
 */
function normalizeImage( image ) {
	if ( ! image || typeof image !== 'object' || ! toText( image.src ) ) {
		return null;
	}
	return {
		src: image.src,
		srcset: toText( image.srcset ),
		sizes: toText( image.sizes ),
		width: toCount( image.width ),
		height: toCount( image.height ),
	};
}

/**
 * Keep only well-formed video data (see PostCardMapper::VIDEO_FILTER).
 * Missing type/id stay null so their data attributes are left out.
 *
 * @param {unknown} video Raw video.
 * @return {{url: string, type: string|null, id: number|null}|null} Video or null.
 */
function normalizeVideo( video ) {
	if ( ! video || typeof video !== 'object' || ! toText( video.url ) ) {
		return null;
	}
	return {
		url: video.url,
		type: toText( video.type ) || null,
		id: Number.isInteger( video.id ) && video.id > 0 ? video.id : null,
	};
}

/**
 * Keep only well-formed cards (positive id and a URL).
 *
 * @param {unknown} item Raw card.
 * @return {Object|null} Card or null.
 */
function normalizeItem( item ) {
	if ( ! item || typeof item !== 'object' ) {
		return null;
	}
	if (
		! ( Number.isInteger( item.id ) && item.id > 0 ) ||
		! toText( item.url )
	) {
		return null;
	}
	return {
		id: item.id,
		title: toText( item.title ),
		url: item.url,
		excerpt: toText( item.excerpt ),
		date: toText( item.date ),
		datetime: toText( item.datetime ),
		image: normalizeImage( item.image ),
		video: normalizeVideo( item.video ),
	};
}

/**
 * Validate and normalize a response body.
 *
 * @param {unknown} body Parsed JSON.
 * @return {{items: Object[], total: number, totalPages: number, page: number, hasMore: boolean, summary: string}} Result.
 * @throws {GalleryRequestError} When the body is not a gallery response.
 */
export function normalizeResponse( body ) {
	if ( ! body || typeof body !== 'object' || ! Array.isArray( body.items ) ) {
		throw new GalleryRequestError( 'Unexpected response shape.' );
	}

	const page = Math.max( 1, toCount( body.page ) );
	const totalPages = toCount( body.totalPages );

	return {
		items: body.items.map( normalizeItem ).filter( Boolean ),
		total: toCount( body.total ),
		totalPages,
		page,
		hasMore: page < totalPages,
		summary: toText( body.summary ),
	};
}

/**
 * Fetch one page.
 *
 * @param {string}       url               Request URL.
 * @param {Object}       options           Options.
 * @param {typeof fetch} options.fetchImpl fetch implementation.
 * @param {AbortSignal}  [options.signal]  Abort signal.
 * @return {Promise<Object>} Normalized result.
 */
export async function fetchGalleryPage( url, { fetchImpl, signal } ) {
	const response = await fetchImpl( url, {
		headers: { Accept: 'application/json' },
		credentials: 'same-origin',
		signal,
	} );

	if ( ! response.ok ) {
		throw new GalleryRequestError(
			`Gallery request failed with HTTP ${ response.status }.`,
			response.status
		);
	}

	let body;
	try {
		body = await response.json();
	} catch {
		throw new GalleryRequestError( 'Gallery response is not JSON.' );
	}

	return normalizeResponse( body );
}
