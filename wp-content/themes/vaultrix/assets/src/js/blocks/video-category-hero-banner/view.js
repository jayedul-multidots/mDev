( function ( $ ) {
	'use strict';

	function buildTrailerPopupHtml( url, type ) {
		const typeAttr = type ? ` type="${ type }"` : '';

		return `
			<div class="watch-video-loader" id="watch-video-loader"></div>
			<div class="watch-video-content watch-trailer-content" id="watch-video-content">
				<div class="watch-trailer-back-button"></div>
				<video controls playsinline preload="metadata">
					<source src="${ url }"${ typeAttr }>
				</video>
			</div>
		`;
	}

	document.addEventListener(
		'click',
		function ( event ) {
			const button = event.target.closest(
				'.video-category-hero-banner .v-watch-trailer[data-trailer-url]'
			);

			if ( ! button ) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			const trailerUrl = button.getAttribute( 'data-trailer-url' );
			const trailerType =
				button.getAttribute( 'data-trailer-type' ) || '';

			if ( ! trailerUrl ) {
				return;
			}

			$( 'html' ).addClass( 'video-video-popup-show' );
			$( '.video-watch-popup' ).addClass( 'open' );
			$( '.video-watch-popup' )
				.empty()
				.html( buildTrailerPopupHtml( trailerUrl, trailerType ) );

			setTimeout( () => {
				$( '.video-watch-popup .watch-video-loader' ).remove();

				const video = $( '.video-watch-popup video' ).get( 0 );

				if ( video ) {
					const playPromise = video.play();

					if ( playPromise !== undefined ) {
						playPromise.catch( () => {} );
					}
				}
			}, 500 );
		},
		true
	);
} )( jQuery );
