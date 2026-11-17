/**
 * Covers lib/vote-storage.js and lib/counts.js.
 */
import { describe, expect, it } from 'vitest';

import { META_KEYS, readCounts } from '../lib/counts';
import {
	getStorage,
	readVote,
	saveVote,
	storageKey,
} from '../lib/vote-storage';
import { memoryStorage, throwingStorage } from './helpers';

describe( 'vote storage', () => {
	it( 'stores and reads a vote per post', () => {
		const storage = memoryStorage();

		expect( saveVote( storage, 3, 'yes' ) ).toBe( true );
		expect( readVote( storage, 3 ) ).toBe( 'yes' );
		expect( readVote( storage, 4 ) ).toBeNull();
		expect( storageKey( 3 ) ).toBe( 'mdev-gb/helpful-poll:3' );
	} );

	it( 'refuses to store anything but yes or no', () => {
		const storage = memoryStorage();

		expect( saveVote( storage, 3, 'maybe' ) ).toBe( false );
		expect( storage.setItem ).not.toHaveBeenCalled();
	} );

	it( 'treats missing or blocked storage as "not voted"', () => {
		expect( readVote( null, 3 ) ).toBeNull();
		expect( saveVote( null, 3, 'yes' ) ).toBe( false );
		expect( readVote( throwingStorage(), 3 ) ).toBeNull();
		expect( saveVote( throwingStorage(), 3, 'yes' ) ).toBe( false );
	} );

	it( 'returns null when reading window.localStorage throws', () => {
		const win = {
			get localStorage() {
				throw new Error( 'SecurityError' );
			},
		};

		expect( getStorage( win ) ).toBeNull();
	} );
} );

describe( 'readCounts', () => {
	it( 'reads the counts from post meta', () => {
		expect(
			readCounts( { [ META_KEYS.yes ]: 12, [ META_KEYS.no ]: 3 } )
		).toEqual( { yes: 12, no: 3 } );
	} );

	it( 'normalizes junk to zero', () => {
		expect(
			readCounts( { [ META_KEYS.yes ]: -4, [ META_KEYS.no ]: 'x' } )
		).toEqual( { yes: 0, no: 0 } );
	} );

	it.each( [ undefined, null, [], { other: 1 } ] )(
		'returns null when the counts are not exposed (%j)',
		( meta ) => {
			expect( readCounts( meta ) ).toBeNull();
		}
	);
} );
