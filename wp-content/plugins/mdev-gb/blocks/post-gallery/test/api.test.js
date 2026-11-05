/**
 * Response normalization: video cards.
 */
import { describe, expect, it } from 'vitest';

import { normalizeResponse } from '../lib/api';
import { card } from './helpers';

const respond = ( items ) =>
	normalizeResponse( { items, total: items.length, totalPages: 1, page: 1 } );

describe( 'normalizeResponse video', () => {
	it( 'keeps a well-formed video', () => {
		const video = {
			url: 'https://example.test/v.mp4',
			type: 'video/mp4',
			id: 42,
		};

		expect(
			respond( [ { ...card( 1 ), video } ] ).items[ 0 ].video
		).toEqual( video );
	} );

	it( 'nulls a missing type and id so their attributes are omitted', () => {
		const video = { url: 'https://example.test/v.mp4', type: '', id: 0 };

		expect(
			respond( [ { ...card( 1 ), video } ] ).items[ 0 ].video
		).toEqual( {
			url: 'https://example.test/v.mp4',
			type: null,
			id: null,
		} );
	} );

	it.each( [
		[ 'missing', undefined ],
		[ 'not an object', 'https://example.test/v.mp4' ],
		[ 'no url', { type: 'video/mp4', id: 1 } ],
	] )( 'drops a %s video', ( _label, video ) => {
		expect(
			respond( [ { ...card( 1 ), video } ] ).items[ 0 ].video
		).toBeNull();
	} );
} );
