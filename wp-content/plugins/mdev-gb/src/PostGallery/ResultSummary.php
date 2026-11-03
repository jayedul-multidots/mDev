<?php
/**
 * Human readable result summaries.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

/**
 * The text announced in the gallery's live region ("12 posts found …").
 *
 * Built on the server for both the first render and every REST response, so
 * plural forms are correct in every language (the browser just displays it).
 *
 * @since 0.2.0
 */
final class ResultSummary {

	/**
	 * Summary for a result.
	 *
	 * @since 0.2.0
	 *
	 * @param GalleryResult $result Result page.
	 * @param string        $search Sanitized search term ('' for none).
	 * @return string Plain text (escape on output).
	 */
	public function describe( GalleryResult $result, string $search ): string {
		if ( 0 === $result->total ) {
			return '' === $search
				? __( 'No posts found.', 'mdev-gb' )
				/* translators: %s: search term. */
				: sprintf( __( 'No posts match “%s”.', 'mdev-gb' ), $search );
		}

		$count = number_format_i18n( $result->total );

		if ( '' === $search ) {
			/* translators: %s: number of posts. */
			return sprintf( _n( '%s post found.', '%s posts found.', $result->total, 'mdev-gb' ), $count );
		}

		return sprintf(
			/* translators: 1: number of posts, 2: search term. */
			_n( '%1$s post found for “%2$s”.', '%1$s posts found for “%2$s”.', $result->total, 'mdev-gb' ),
			$count,
			$search
		);
	}
}
