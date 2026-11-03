/**
 * Covers blocks/callout/tones.js.
 */
import { describe, expect, it } from 'vitest';

import { DEFAULT_TONE, TONES, getToneOptions, normalizeTone } from '../tones';

describe( 'normalizeTone', () => {
	it( 'keeps every allowed tone', () => {
		TONES.forEach( ( tone ) => {
			expect( normalizeTone( tone ) ).toBe( tone );
		} );
	} );

	it( 'falls back to the default for unknown or non-string values', () => {
		expect( normalizeTone( 'purple' ) ).toBe( DEFAULT_TONE );
		expect( normalizeTone( undefined ) ).toBe( DEFAULT_TONE );
		expect( normalizeTone( 42 ) ).toBe( DEFAULT_TONE );
		expect( normalizeTone( '<script>' ) ).toBe( DEFAULT_TONE );
	} );
} );

describe( 'getToneOptions', () => {
	it( 'offers exactly the allowed tones', () => {
		expect( getToneOptions().map( ( option ) => option.value ) ).toEqual(
			TONES
		);
	} );
} );
