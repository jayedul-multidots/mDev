/**
 * Loads a page of results into a gallery's Interactivity API context.
 *
 * Plain function over a plain object, so it is unit-testable without the
 * Interactivity API; view.js passes in the reactive context.
 */
import { fetchGalleryPage, buildRequestUrl } from './api';
import { STALE } from './latest-request';
import { buildUrl } from './url-state';

/**
 * Identity of the result set currently shown (search + sort).
 *
 * @param {string} search Search term.
 * @param {string} sort   Sort value.
 * @return {string} Key.
 */
export const queryKey = ( search, sort ) => `${ search.trim() }\u0000${ sort }`;

/**
 * Append without duplicates (posts can shift between pages when content is
 * published while someone is browsing).
 *
 * @param {Object[]} current Current cards.
 * @param {Object[]} next    New cards.
 * @return {Object[]} Merged cards.
 */
export function mergeItems( current, next ) {
	const seen = new Set( current.map( ( item ) => item.id ) );
	return [ ...current, ...next.filter( ( item ) => ! seen.has( item.id ) ) ];
}

/**
 * Load results into the context.
 *
 * @param {Object}                                   ctx              Gallery context (mutated).
 * @param {Object}                                   options          Options.
 * @param {number}                                   [options.page]   Page to load.
 * @param {boolean}                                  [options.append] Append (Load more) instead of replace.
 * @param {boolean}                                  [options.force]  Reload even if search/sort are unchanged.
 * @param {Object}                                   deps             Dependencies.
 * @param {import('./latest-request').LatestRequest} deps.request     createLatestRequest() runner.
 * @param {typeof fetch}                             deps.fetchImpl   fetch implementation.
 * @param {{href: string}}                           deps.location    Current location.
 * @param {Pick<History, 'state'|'replaceState'>}    [deps.history]   History API.
 * @return {Promise<'ok'|'unchanged'|'stale'|'error'>} Outcome.
 */
export async function loadGallery(
	ctx,
	{ page = 1, append = false, force = false },
	deps
) {
	const key = queryKey( ctx.search, ctx.sort );
	if (
		! append &&
		! force &&
		key === queryKey( ctx.loadedSearch, ctx.loadedSort )
	) {
		return 'unchanged';
	}

	const search = ctx.search;
	const sort = ctx.sort;
	ctx.isLoading = true;
	ctx.error = '';

	const url = buildRequestUrl( ctx.restUrl, {
		search,
		sort,
		page,
		defaultSort: ctx.defaultSort,
		settings: ctx.settings,
	} );

	let result;
	try {
		result = await deps.request.run( ( signal ) =>
			fetchGalleryPage( url, { fetchImpl: deps.fetchImpl, signal } )
		);
	} catch {
		ctx.isLoading = false;
		ctx.error = ctx.errorMessage;
		ctx.retry = { page, append };
		return 'error';
	}

	if ( result === STALE ) {
		// A newer request owns the loading state now.
		return 'stale';
	}

	ctx.items = append ? mergeItems( ctx.items, result.items ) : result.items;
	ctx.total = result.total;
	ctx.totalPages = result.totalPages;
	ctx.page = result.page;
	ctx.hasMore = result.hasMore;
	ctx.summary = result.summary;
	ctx.loadedSearch = search;
	ctx.loadedSort = sort;
	ctx.retry = null;
	ctx.nextUrl = result.hasMore
		? buildUrl(
				deps.location.href,
				ctx.prefix,
				{ search, sort, page: result.page + 1 },
				ctx.defaultSort
			)
		: '';
	ctx.isLoading = false;

	// Shareable URL for the current filters. Page is left out on purpose:
	// reloading should show the first page, not only the last appended one.
	deps.history?.replaceState(
		deps.history.state,
		'',
		buildUrl(
			deps.location.href,
			ctx.prefix,
			{ search, sort },
			ctx.defaultSort
		)
	);

	return 'ok';
}
