/**
 * Covers lib/gallery-controller.js.
 *
 * Techniques: a fake fetch (vi.fn resolving canned responses), a spy on the
 * History API, and controllable promises to force a network race.
 */
import { describe, expect, it, vi } from 'vitest';

import { loadGallery } from '../lib/gallery-controller';
import { createLatestRequest } from '../lib/latest-request';
import {
	PAGE_URL,
	card,
	fetchReturning,
	makeContext,
	pageBody,
} from './helpers';

const makeDeps = ( fetchImpl ) => ( {
	request: createLatestRequest(),
	fetchImpl,
	location: { href: PAGE_URL },
	history: { state: null, replaceState: vi.fn() },
} );

describe( 'loadGallery', () => {
	it( 'replaces results and syncs the address bar (without the page)', async () => {
		const ctx = makeContext( { search: 'river', sort: 'title-asc' } );
		const fetchImpl = fetchReturning(
			pageBody( { page: 1, totalPages: 1 } )
		);
		const deps = makeDeps( fetchImpl );

		await expect( loadGallery( ctx, { page: 1 }, deps ) ).resolves.toBe(
			'ok'
		);

		expect( ctx.items.map( ( item ) => item.id ) ).toEqual( [ 3, 4 ] );
		expect( ctx ).toMatchObject( {
			hasMore: false,
			isLoading: false,
			loadedSearch: 'river',
		} );
		expect(
			new URL( fetchImpl.mock.calls[ 0 ][ 0 ] ).searchParams.get(
				'per_page'
			)
		).toBe( '2' );

		const url = new URL( deps.history.replaceState.mock.calls[ 0 ][ 2 ] );
		expect( url.searchParams.get( 'pg1_q' ) ).toBe( 'river' );
		expect( url.searchParams.has( 'pg1_page' ) ).toBe( false );
	} );

	it( 'appends Load more pages without duplicates', async () => {
		const ctx = makeContext();
		const fetchImpl = fetchReturning(
			pageBody( { items: [ card( 2 ), card( 3 ) ], page: 2 } )
		);

		await loadGallery(
			ctx,
			{ page: 2, append: true },
			makeDeps( fetchImpl )
		);

		expect( ctx.items.map( ( item ) => item.id ) ).toEqual( [ 1, 2, 3 ] );
		expect( new URL( ctx.nextUrl ).searchParams.get( 'pg1_page' ) ).toBe(
			'3'
		);
	} );

	it( 'lets the newest request win when responses arrive out of order', async () => {
		const ctx = makeContext();
		const responders = [];
		const fetchImpl = vi.fn(
			() => new Promise( ( resolve ) => responders.push( resolve ) )
		);
		const deps = makeDeps( fetchImpl );
		const respond = ( index, body ) =>
			responders[ index ]( {
				ok: true,
				status: 200,
				json: async () => body,
			} );

		ctx.search = 'ri';
		const older = loadGallery( ctx, { page: 1 }, deps );
		ctx.search = 'river';
		const newer = loadGallery( ctx, { page: 1 }, deps );

		respond( 1, pageBody( { items: [ card( 9 ) ], page: 1 } ) );
		await expect( newer ).resolves.toBe( 'ok' );
		respond( 0, pageBody( { items: [ card( 8 ) ], page: 1 } ) );
		await expect( older ).resolves.toBe( 'stale' );

		expect( ctx.items.map( ( item ) => item.id ) ).toEqual( [ 9 ] );
	} );

	it( 'shows an error and remembers what to retry', async () => {
		const ctx = makeContext( { search: 'x' } );
		const fetchImpl = vi
			.fn()
			.mockResolvedValue( { ok: false, status: 500 } );

		await expect(
			loadGallery( ctx, { page: 2, append: true }, makeDeps( fetchImpl ) )
		).resolves.toBe( 'error' );

		expect( ctx ).toMatchObject( {
			isLoading: false,
			error: 'Could not load posts.',
			retry: { page: 2, append: true },
		} );
	} );
} );
