/**
 * Shared fixtures for the helpful poll tests.
 */
import { vi } from 'vitest';

export const ENDPOINT =
	'https://example.test/wp-json/mdev-gb/v1/helpful-poll/7';

/**
 * Poll context as PollView::build() renders it.
 *
 * @param {Object} overrides Overrides.
 * @return {Object} Context.
 */
export const makeContext = ( overrides = {} ) => ( {
	postId: 7,
	endpoint: ENDPOINT,
	hasVoted: false,
	isSubmitting: false,
	message: '',
	error: '',
	thanks: 'Thanks!',
	errorMessage: 'Could not send.',
	...overrides,
} );

/**
 * In-memory Storage.
 *
 * @param {Object<string, string>} initial Initial items.
 * @return {Storage} Storage.
 */
export function memoryStorage( initial = {} ) {
	const items = new Map( Object.entries( initial ) );
	return {
		getItem: ( key ) => ( items.has( key ) ? items.get( key ) : null ),
		setItem: vi.fn( ( key, value ) => items.set( key, String( value ) ) ),
		removeItem: ( key ) => items.delete( key ),
	};
}

/**
 * Storage whose every call throws, like blocked storage in privacy modes.
 *
 * @return {Storage} Storage.
 */
export function throwingStorage() {
	const fail = () => {
		throw new Error( 'SecurityError' );
	};
	return { getItem: fail, setItem: fail, removeItem: fail };
}

/**
 * fetch resolving to a response with the given status.
 *
 * @param {number} status HTTP status.
 * @return {import("vitest").Mock} Mock fetch.
 */
export const fetchWithStatus = ( status ) =>
	vi.fn().mockResolvedValue( {
		ok: status >= 200 && status < 300,
		status,
		json: async () => ( {} ),
	} );
