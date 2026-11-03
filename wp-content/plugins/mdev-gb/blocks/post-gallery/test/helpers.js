/**
 * Shared fixtures for post gallery tests.
 */
import { vi } from 'vitest';

export const PAGE_URL = 'https://example.test/gallery/?utm=x';
export const REST_URL = 'https://example.test/wp-json/mdev-gb/v1/post-gallery';

/**
 * A card as returned by the API.
 *
 * @param {number} id Post ID.
 * @return {Object} Card.
 */
export const card = ( id ) => ( {
	id,
	title: `Post ${ id }`,
	url: `https://example.test/?p=${ id }`,
	excerpt: '',
	date: '',
	datetime: '',
	image: null,
} );

/**
 * Gallery context as rendered by the server.
 *
 * @param {Object} overrides Overrides.
 * @return {Object} Context.
 */
export const makeContext = ( overrides = {} ) => ( {
	restUrl: REST_URL,
	prefix: 'pg1_',
	settings: {
		postType: 'post',
		perPage: 2,
		categories: [],
		imageSize: 'large',
	},
	defaultSort: 'newest',
	search: '',
	sort: 'newest',
	loadedSearch: '',
	loadedSort: 'newest',
	retry: null,
	page: 1,
	items: [ card( 1 ), card( 2 ) ],
	total: 5,
	totalPages: 3,
	hasMore: true,
	nextUrl: '?pg1_page=2',
	summary: '5 posts found.',
	isLoading: false,
	error: '',
	errorMessage: 'Could not load posts.',
	...overrides,
} );

/**
 * fetch() double answering with a gallery page.
 *
 * @param {Object} body Response body.
 * @return {import('vitest').Mock} fetch mock.
 */
export const fetchReturning = ( body ) =>
	vi.fn().mockResolvedValue( {
		ok: true,
		status: 200,
		json: async () => body,
	} );

/**
 * A response body.
 *
 * @param {Object} overrides Overrides.
 * @return {Object} Body.
 */
export const pageBody = ( overrides = {} ) => ( {
	items: [ card( 3 ), card( 4 ) ],
	total: 5,
	totalPages: 3,
	page: 2,
	summary: '5 posts found.',
	...overrides,
} );
