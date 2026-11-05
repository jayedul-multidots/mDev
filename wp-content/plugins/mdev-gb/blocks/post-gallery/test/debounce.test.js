/**
 * Covers lib/debounce.js default timers.
 */
import { afterEach, describe, expect, it, vi } from 'vitest';

import { debounce } from '../lib/debounce';

/**
 * A timer function that, like the browser's native ones, throws when called
 * with a `this` other than the global object (e.g. as a method of another
 * object). Node's timers do not check, which hid this bug from unit tests.
 *
 * @param {(...args: unknown[]) => unknown} impl Real behaviour.
 * @return {(...args: unknown[]) => unknown} Strict timer function.
 */
const nativeLike = ( impl ) =>
	function ( ...args ) {
		if ( this !== undefined && this !== globalThis ) {
			throw new TypeError( 'Illegal invocation' );
		}
		return impl( ...args );
	};

describe( 'debounce default timers', () => {
	afterEach( () => {
		vi.unstubAllGlobals();
	} );

	it( 'calls the global timers without rebinding them', () => {
		const setTimeoutSpy = vi.fn( () => 1 );
		const clearTimeoutSpy = vi.fn();
		vi.stubGlobal( 'setTimeout', nativeLike( setTimeoutSpy ) );
		vi.stubGlobal( 'clearTimeout', nativeLike( clearTimeoutSpy ) );

		const debounced = debounce( vi.fn(), 300 );

		expect( () => debounced( 'a' ) ).not.toThrow();
		expect( () => debounced( 'b' ) ).not.toThrow();
		expect( () => debounced.cancel() ).not.toThrow();
		expect( setTimeoutSpy ).toHaveBeenCalledTimes( 2 );
		expect( setTimeoutSpy ).toHaveBeenLastCalledWith(
			expect.any( Function ),
			300
		);
		expect( clearTimeoutSpy ).toHaveBeenCalledTimes( 2 );
	} );
} );
