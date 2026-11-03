/**
 * Front-end store for mdev-gb/post-gallery (Interactivity API).
 *
 * Thin glue: state transitions live in lib/gallery-controller.js. Each gallery
 * on the page has its own context, request runner and search debounce.
 */
import {
	store,
	getContext,
	withScope,
	withSyncEvent,
} from '@wordpress/interactivity';

import { debounce } from './lib/debounce';
import { createLatestRequest } from './lib/latest-request';
import { loadGallery } from './lib/gallery-controller';

export const STORE = 'mdev-gb/post-gallery';
export const SEARCH_DEBOUNCE_MS = 300;

/**
 * @typedef {Object} GalleryInstance
 * @property {import('./lib/latest-request').LatestRequest} request Request runner.
 * @property {import('./lib/debounce').Debounced}           search  Debounced refresh.
 */

/** @type {Map<string, GalleryInstance>} */
const instances = new Map();

/**
 * Per-gallery helpers, created lazily inside the gallery's scope.
 *
 * @param {Object} ctx Gallery context.
 * @return {GalleryInstance} Instance.
 */
function instanceFor( ctx ) {
	let instance = instances.get( ctx.prefix );
	if ( ! instance ) {
		instance = {
			request: createLatestRequest(),
			// withScope keeps getContext() working when the timer fires later.
			search: debounce(
				withScope( () => actions.refresh() ),
				SEARCH_DEBOUNCE_MS
			),
		};
		instances.set( ctx.prefix, instance );
	}
	return instance;
}

/**
 * Browser dependencies for the controller.
 *
 * @param {Object} instance Gallery instance.
 * @return {Object} Dependencies.
 */
function depsFor( instance ) {
	return {
		request: instance.request,
		fetchImpl: ( ...args ) => window.fetch( ...args ),
		location: window.location,
		history: window.history,
	};
}

const { actions } = store( STORE, {
	actions: {
		/**
		 * Search input: update immediately, fetch after typing pauses.
		 *
		 * @param {InputEvent} event Input event.
		 */
		updateSearch( event ) {
			const ctx = getContext();
			ctx.search = event.target.value;
			instanceFor( ctx ).search();
		},

		/**
		 * Sort change: fetch right away.
		 *
		 * @param {Event} event Change event.
		 */
		*updateSort( event ) {
			const ctx = getContext();
			ctx.sort = event.target.value;
			instanceFor( ctx ).search.cancel();
			yield actions.refresh();
		},

		/** Form submit (Enter / Apply): fetch now instead of reloading the page. */
		submit: withSyncEvent( function* ( event ) {
			event.preventDefault();
			const ctx = getContext();
			instanceFor( ctx ).search.cancel();
			yield actions.refresh();
		} ),

		/** "Load more": append the next page instead of following the link. */
		loadMore: withSyncEvent( function* ( event ) {
			event.preventDefault();
			const ctx = getContext();
			if ( ctx.isLoading || ! ctx.hasMore ) {
				return;
			}
			const instance = instanceFor( ctx );
			yield loadGallery(
				ctx,
				{ page: ctx.page + 1, append: true },
				depsFor( instance )
			);
		} ),

		/** Retry the request that failed. */
		*retry() {
			const ctx = getContext();
			const { page = 1, append = false } = ctx.retry ?? {};
			yield loadGallery(
				ctx,
				{ page, append, force: true },
				depsFor( instanceFor( ctx ) )
			);
		},

		/** Load the first page for the current search and sort. */
		*refresh() {
			const ctx = getContext();
			yield loadGallery(
				ctx,
				{ page: 1, append: false },
				depsFor( instanceFor( ctx ) )
			);
		},
	},
} );

export { actions };
