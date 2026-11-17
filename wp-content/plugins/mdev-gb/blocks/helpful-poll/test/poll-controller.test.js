/**
 * Covers lib/poll-controller.js.
 *
 * Techniques: a plain context object standing in for the Interactivity API
 * context, an in-memory Storage, and fake fetch implementations.
 */
import { describe, expect, it, vi } from 'vitest';

import { castVote, restoreVote } from '../lib/poll-controller';
import { storageKey } from '../lib/vote-storage';
import {
	ENDPOINT,
	fetchWithStatus,
	makeContext,
	memoryStorage,
	throwingStorage,
} from './helpers';

describe( 'restoreVote', () => {
	it( 'shows the thanks state for a post this browser voted on', () => {
		const ctx = makeContext();

		expect(
			restoreVote( ctx, memoryStorage( { [ storageKey( 7 ) ]: 'no' } ) )
		).toBe( true );
		expect( ctx ).toMatchObject( { hasVoted: true, message: 'Thanks!' } );
	} );

	it( 'leaves the poll open for other posts and for junk values', () => {
		const ctx = makeContext();
		const storage = memoryStorage( {
			[ storageKey( 8 ) ]: 'yes',
			[ storageKey( 7 ) ]: '<img onerror=alert(1)>',
		} );

		expect( restoreVote( ctx, storage ) ).toBe( false );
		expect( ctx.hasVoted ).toBe( false );
	} );
} );

describe( 'castVote', () => {
	it( 'records the vote, remembers it and shows the thanks', async () => {
		const ctx = makeContext();
		const storage = memoryStorage();
		const fetchImpl = fetchWithStatus( 201 );
		const onRecorded = vi.fn();

		await expect(
			castVote( ctx, 'yes', { fetchImpl, storage, onRecorded } )
		).resolves.toBe( 'ok' );

		const [ url, init ] = fetchImpl.mock.calls[ 0 ];
		expect( url ).toBe( ENDPOINT );
		expect( init ).toMatchObject( {
			method: 'POST',
			credentials: 'omit',
		} );
		expect( JSON.parse( init.body ) ).toEqual( { vote: 'yes' } );
		expect( storage.getItem( storageKey( 7 ) ) ).toBe( 'yes' );
		expect( ctx ).toMatchObject( {
			hasVoted: true,
			isSubmitting: false,
			message: 'Thanks!',
			error: '',
		} );
		expect( onRecorded ).toHaveBeenCalledOnce();
	} );

	it( 'does not send a second vote from the same browser', async () => {
		const ctx = makeContext();
		const fetchImpl = fetchWithStatus( 201 );
		const storage = memoryStorage( { [ storageKey( 7 ) ]: 'no' } );

		await expect(
			castVote( ctx, 'yes', { fetchImpl, storage } )
		).resolves.toBe( 'skipped' );

		expect( fetchImpl ).not.toHaveBeenCalled();
		expect( ctx.hasVoted ).toBe( true );
		expect( storage.getItem( storageKey( 7 ) ) ).toBe( 'no' );
	} );

	it( 'ignores clicks while a vote is in flight', async () => {
		let resolve;
		const fetchImpl = vi.fn(
			() =>
				new Promise( ( r ) => {
					resolve = r;
				} )
		);
		const ctx = makeContext();
		const deps = { fetchImpl, storage: memoryStorage() };

		const first = castVote( ctx, 'yes', deps );
		expect( ctx.isSubmitting ).toBe( true );
		await expect( castVote( ctx, 'no', deps ) ).resolves.toBe( 'skipped' );

		resolve( { ok: true, status: 201 } );
		await expect( first ).resolves.toBe( 'ok' );
		expect( fetchImpl ).toHaveBeenCalledOnce();
	} );

	it.each( [
		[ 'a server error', fetchWithStatus( 500 ) ],
		[ 'a rejected post', fetchWithStatus( 404 ) ],
		[ 'a network error', vi.fn().mockRejectedValue( new TypeError() ) ],
	] )(
		'shows an error and allows a retry after %s',
		async ( _label, fetchImpl ) => {
			const ctx = makeContext();
			const storage = memoryStorage();

			await expect(
				castVote( ctx, 'no', { fetchImpl, storage } )
			).resolves.toBe( 'error' );

			expect( ctx ).toMatchObject( {
				hasVoted: false,
				isSubmitting: false,
				error: 'Could not send.',
			} );
			expect( storage.setItem ).not.toHaveBeenCalled();

			await expect(
				castVote( ctx, 'no', {
					fetchImpl: fetchWithStatus( 201 ),
					storage,
				} )
			).resolves.toBe( 'ok' );
			expect( ctx.error ).toBe( '' );
		}
	);

	it( 'still votes once per page when storage is blocked', async () => {
		const ctx = makeContext();
		const fetchImpl = fetchWithStatus( 201 );
		const deps = { fetchImpl, storage: throwingStorage() };

		await expect( castVote( ctx, 'yes', deps ) ).resolves.toBe( 'ok' );
		await expect( castVote( ctx, 'yes', deps ) ).resolves.toBe( 'skipped' );
		expect( fetchImpl ).toHaveBeenCalledOnce();
	} );

	it( 'rejects values other than yes and no without a request', async () => {
		const ctx = makeContext();
		const fetchImpl = fetchWithStatus( 201 );

		await expect(
			castVote( ctx, 'maybe', { fetchImpl, storage: memoryStorage() } )
		).resolves.toBe( 'error' );
		expect( fetchImpl ).not.toHaveBeenCalled();
	} );
} );
