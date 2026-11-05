/**
 * mDev Post Gallery listing videos: clicking a card opens the theme's video
 * popup instead of the video post, which has no page of its own.
 *
 * Runs against an existing page (E2E_GALLERY_PATH, default /mdev-gallery/)
 * holding an mDev Post Gallery block of videos (deep_pockets_videos), with
 * the vaultrix theme active: it supplies each video's file through the
 * `mdev_gb_post_gallery_card_video` filter and owns the popup. "Load more"
 * needs more videos than the block shows per page.
 */
const { test, expect } = require( '@playwright/test' );

const GALLERY_PATH = process.env.E2E_GALLERY_PATH || '/mdev-gallery/';

const cardTitles = ( page ) =>
	page.locator( '.wp-block-mdev-gb-post-gallery__title a' );
const popup = ( page ) => page.locator( '.video-watch-popup' );

/**
 * Click a card link and check the popup plays that card's video in place.
 *
 * @param {import('@playwright/test').Page}    page Page.
 * @param {import('@playwright/test').Locator} link Card link to click.
 */
async function expectLinkOpensPopup( page, link ) {
	const pageUrl = page.url();
	const videoUrl = await link.getAttribute( 'data-video-url' );
	const videoId = await link.getAttribute( 'data-video-id' );

	expect( videoUrl ).toBeTruthy();

	await link.click();

	await expect( popup( page ) ).toHaveClass( /\bopen\b/ );
	await expect( popup( page ).locator( 'video source' ) ).toHaveAttribute(
		'src',
		videoUrl
	);
	if ( videoId ) {
		await expect( popup( page ).locator( 'video' ) ).toHaveAttribute(
			'data-video-id',
			videoId
		);
	}
	// The click must not navigate to the video post (or the file itself).
	expect( page.url() ).toBe( pageUrl );
}

/**
 * Close the popup with its back button.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function closePopup( page ) {
	await popup( page ).locator( '.watch-video-back-button' ).click();
	await expect( popup( page ) ).not.toHaveClass( /\bopen\b/ );
	await expect( popup( page ).locator( 'video' ) ).toHaveCount( 0 );
}

test.describe( 'Post gallery video cards', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( GALLERY_PATH );
		await expect( cardTitles( page ).first() ).toBeVisible();
	} );

	test( 'cards link to the video file, not the video post', async ( {
		page,
	} ) => {
		const cards = await cardTitles( page ).count();
		const links = page.locator(
			'.wp-block-mdev-gb-post-gallery__card a[data-video-url]'
		);
		// Image and title link of every card.
		await expect( links ).toHaveCount( cards * 2 );

		for ( const link of await links.all() ) {
			const href = await link.getAttribute( 'href' );
			expect( href ).toBe( await link.getAttribute( 'data-video-url' ) );
			expect( href ).not.toContain( '/vaultrix-videos/' );
		}
	} );

	test( 'clicking a card title or image opens the video popup', async ( {
		page,
	} ) => {
		await expectLinkOpensPopup( page, cardTitles( page ).first() );
		await closePopup( page );

		await expectLinkOpensPopup(
			page,
			page.locator( '.wp-block-mdev-gb-post-gallery__media' ).nth( 1 )
		);
		await closePopup( page );
	} );

	test( 'cards added by "Load more" open the video popup too', async ( {
		page,
	} ) => {
		const firstPage = await cardTitles( page ).count();
		const loadMore = page.getByRole( 'link', { name: 'Load more' } );
		test.skip(
			! ( await loadMore.isVisible() ),
			'All videos fit on the first page: nothing to load.'
		);

		await loadMore.click();
		await expect
			.poll( () => cardTitles( page ).count() )
			.toBeGreaterThan( firstPage );

		await expectLinkOpensPopup( page, cardTitles( page ).nth( firstPage ) );
		await closePopup( page );
	} );
} );
