/* eslint-disable no-else-return */
/* eslint-disable no-alert */
( function ( $ ) {
	'use strict';
	$( document ).ready( function () {
		$( document ).on(
			'click',
			'.playlist-video-listing .added-to-playlist',
			function () {
				// location.reload();
				const postID = $( this )
					.parents( '.playlist-popup-button' )
					.attr( 'data-post-id' );
				const queryString = window.location.search;
				const urlParams = new URLSearchParams( queryString );
				const playlistSlug = urlParams.get( 'playlist' );
				$.ajax( {
					type: 'GET',
					data: {
						action: 'dp_remove_video_from_playlist',
						pid: postID,
						playlist_slug: playlistSlug,
					},
				    url: siteConfig.ajaxUrl, // eslint-disable-line
					beforeSend() {
						$(
							'.playlist-video-listing .playlist-video-listing__video-listing'
						).remove(); // phpcs:ignore
						$(
							'.playlist-video-listing .playlist-video-listing__wrapper'
						).append(
							'<div class="playlist-video-listing__loader"><span class="fa fa-spinner fa-pulse"></span></div>'
						);
					},
					success( response ) {
						$( '.playlist-video-listing__loader' ).remove();
						$(
							'.playlist-video-listing .playlist-video-listing__wrapper'
						).append( response );
					},
				} );
				// return false;
			}
		);
	} );
} )( jQuery );
