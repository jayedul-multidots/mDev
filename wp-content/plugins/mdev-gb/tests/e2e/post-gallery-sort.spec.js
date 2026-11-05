/**
 * mDev Post Gallery sort: choosing a title order reorders the cards.
 *
 * Runs against an existing page (E2E_GALLERY_PATH, default /mdev-gallery/)
 * holding an mDev Post Gallery block with the sort control shown (default
 * sort: newest first). Checks the order of the titles' first letters, so
 * the titles are expected to start with letters.
 */
const { test, expect } = require( '@playwright/test' );

const GALLERY_PATH = process.env.E2E_GALLERY_PATH || '/mdev-gallery/';

const SORTS = [
	{ label: 'Title (A–Z)', value: 'title-asc', direction: 1 },
	{ label: 'Title (Z–A)', value: 'title-desc', direction: -1 },
];

test.describe( 'Post gallery sort', () => {
	for ( const { label, value, direction } of SORTS ) {
		test( `"${ label }" orders the titles by first letter`, async ( {
			page,
		} ) => {
			await page.goto( GALLERY_PATH );

			const titles = page.locator(
				'.wp-block-mdev-gb-post-gallery__title a'
			);
			await expect( titles.first() ).toBeVisible();

			await page.getByLabel( 'Sort by' ).selectOption( { label } );

			// The address bar is updated once the reordered cards are in.
			await expect( page ).toHaveURL(
				new RegExp( `[?&]pg\\d+_sort=${ value }(&|$)` )
			);

			const initials = ( await titles.allTextContents() ).map(
				( title ) => title.trim().charAt( 0 ).toUpperCase()
			);
			expect( initials.length ).toBeGreaterThan( 1 );

			for ( let i = 1; i < initials.length; i++ ) {
				const order =
					initials[ i - 1 ].localeCompare( initials[ i ] ) *
					direction;
				expect(
					order,
					`"${ initials[ i - 1 ] }" before "${ initials[ i ] }" (${ initials.join(
						' '
					) })`
				).toBeLessThanOrEqual( 0 );
			}
		} );
	}
} );
