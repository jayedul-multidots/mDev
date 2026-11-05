( function ( $ ) {
	'use strict';
	const videoSliderWrp = [];

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
				'.trending-now-section .play-from-beginning[data-trailer-url]'
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

			const wrapper = button.closest( '.video-popup-wrapper' );

			if ( wrapper ) {
				wrapper.classList.remove( 'video-mini-popup-show' );

				const hoverVideo = wrapper.querySelector(
					'.video-hover-popup video'
				);

				if ( hoverVideo ) {
					hoverVideo.pause();
				}
			}

			$( 'html' ).removeClass( 'html-video-mini-popup-show' );
			$( '.video-popup-wrapper-with-slider' ).removeClass(
				'video-popup-open'
			);

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

	$( document ).ready( function () {
		videoSliderFunc();
		$( window ).on( 'resize', function () {
			videoSliderFunc();
		} );

		$( '.trending-now-section__video-series-wrapper' ).on(
			'setPosition',
			function ( event, slick ) {
				// Remove the class from all slides
				$(
					'.trending-now-section__video-series-wrapper .slick-slide'
				).removeClass( 'slider-active-last' );

				// Calculate the index of the 6th visible slide based on window width and add class accordingly
				let indexToAddClass;
				const windowWidth = $( window ).width();
				if ( windowWidth < 480 ) {
					indexToAddClass = 1;
				} else if ( windowWidth < 576 ) {
					indexToAddClass = 2;
				} else {
					indexToAddClass = 3;
				}

				if ( indexToAddClass ) {
					// Add the class to the calculated index
					$(
						'.trending-now-section__video-series-wrapper .slick-active'
					)
						.eq( indexToAddClass )
						.addClass( 'slider-active-last' );
				}
			}
		);
	} );

	function videoSliderFunc() {
		$( '.trending-now-section' ).each( function ( index ) {
			const sliderMain = $( this ).find(
				'.trending-now-section__video-series-wrapper'
			);
			const sliderConfig = {
				infinite: false,
				slidesToShow: 4,
				slidesToScroll: 1,
				dots: false,
				arrows: true,
				autoplay: false,
				responsive: [
					{
						breakpoint: 576,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 1,
						},
					},
					{
						breakpoint: 480,
						settings: {
							slidesToShow: 2.2,
							slidesToScroll: 1,
						},
					},
				],
			};
			if ( videoSliderWrp[ index ] ) {
				videoSliderWrp[ index ].slick( 'refresh' );
			} else {
				videoSliderWrp[ index ] = sliderMain.slick( sliderConfig );
			}
		} );
	}
} )( jQuery );
