/**
 * Covers view.js (Interactivity API store wiring).
 *
 * Techniques: a module mock replacing `@wordpress/interactivity` (it runs
 * generator actions like the real store and lets each test set the context),
 * fake timers for the search debounce, and a faked browser `window`.
 */
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import { PAGE_URL, card, makeContext, pageBody } from './helpers';

const scope = vi.hoisted( () => ( { context: null } ) );

vi.mock( '@wordpress/interactivity', () => {
	const run = async ( iterator ) => {
		let step = iterator.next();
		while ( ! step.done ) {
			step = iterator.next( await step.value );
		}
		return step.value;
	};
	return {
		store: ( namespace, definition ) => {
			Object.entries( definition.actions ).forEach( ( [ name, fn ] ) => {
				definition.actions[ name ] = ( ...args ) => {
					const result = fn( ...args );
					return typeof result?.next === 'function'
						? run( result )
						: result;
				};
			} );
			return definition;
		},
		getContext: () => scope.context,
		withScope: ( fn ) => {
			const captured = scope.context;
			return ( ...args ) => {
				scope.context = captured;
				return fn( ...args );
			};
		},
		withSyncEvent: ( fn ) => fn,
	};
} );

const { actions, SEARCH_DEBOUNCE_MS } = await import( '../view' );

let instance = 0;
const newContext = ( overrides = {} ) =>
	makeContext( { prefix: `pg${ ++instance }_`, ...overrides } );
const event = ( value ) => ( { target: { value }, preventDefault: vi.fn() } );
const flushPromises = () =>
	new Promise( ( resolve ) => setImmediate( resolve ) );
const respondWith = ( body ) =>
	window.fetch.mockResolvedValue( {
		ok: true,
		status: 200,
		json: async () => body,
	} );

beforeEach( () => {
	globalThis.window = {
		fetch: vi.fn(),
		location: { href: PAGE_URL },
		history: { state: null, replaceState: vi.fn() },
	};
	respondWith( pageBody( { page: 1 } ) );
} );

afterEach( () => {
	vi.useRealTimers();
	delete globalThis.window;
} );

describe( 'post gallery store', () => {
	it( 'debounces typing into one request for the final term', async () => {
		vi.useFakeTimers( { toFake: [ 'setTimeout', 'clearTimeout' ] } );
		scope.context = newContext();

		[ 'r', 'ri', 'river' ].forEach( ( value ) =>
			actions.updateSearch( event( value ) )
		);
		expect( window.fetch ).not.toHaveBeenCalled();

		vi.advanceTimersByTime( SEARCH_DEBOUNCE_MS );
		await flushPromises();

		expect( window.fetch ).toHaveBeenCalledOnce();
		expect(
			new URL( window.fetch.mock.calls[ 0 ][ 0 ] ).searchParams.get(
				'search'
			)
		).toBe( 'river' );
	} );

	it( 'Load more appends instead of following the link', async () => {
		scope.context = newContext();
		respondWith( pageBody( { items: [ card( 3 ) ], page: 2 } ) );
		const click = event();

		await actions.loadMore( click );

		expect( click.preventDefault ).toHaveBeenCalledOnce();
		expect( scope.context.items.map( ( item ) => item.id ) ).toEqual( [
			1, 2, 3,
		] );
	} );

	it( 'Load more is ignored while a request is running', async () => {
		scope.context = newContext( { isLoading: true } );

		await actions.loadMore( event() );

		expect( window.fetch ).not.toHaveBeenCalled();
	} );
} );
