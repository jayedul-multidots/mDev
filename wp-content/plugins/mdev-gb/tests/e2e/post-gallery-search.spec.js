/**
 * mDev Post Gallery search: typing a term shows only matching cards.
 *
 * Runs against an existing page (E2E_GALLERY_PATH, default /mdev-gallery/)
 * holding an mDev Post Gallery block with the search field shown, and
 * content whose titles contain SEARCH_TERM. WordPress search also matches
 * content and excerpts, so every post matching SEARCH_TERM is expected to
 * have it in its title.
 */
const { test, expect } = require( '@playwright/test' );

const GALLERY_PATH = process.env.E2E_GALLERY_PATH || '/mdev-gallery/';
const SEARCH_TERM = 'building';

test.describe( 'Post gallery search', () => {
	test( `searching "${ SEARCH_TERM }" only shows cards with it in the title`, async ( {
		page,
	} ) => {
		await page.goto( GALLERY_PATH );

		const titles = page.locator(
			'.wp-block-mdev-gb-post-gallery__title a'
		);
		await expect( titles.first() ).toBeVisible();

		await page.getByLabel( 'Search posts' ).fill( SEARCH_TERM );

		// The summary names the term once the results for it have loaded.
		await expect(
			page.locator( '.wp-block-mdev-gb-post-gallery__summary' )
		).toContainText( `for “${ SEARCH_TERM }”` );

		const results = await titles.allTextContents();
		expect( results.length ).toBeGreaterThan( 0 );
		for ( const title of results ) {
			expect( title.toLowerCase() ).toContain( SEARCH_TERM );
		}
	} );
} );
