( function ( $ ) {
	'use strict';
	const videoSliderWrp = [];
	$( document ).ready( function () {
		$( window ).on( 'resize', function () {
			if ( $( window ).width() < 768 ) {
				videoSliderFunc();
			}
		} );
		if ( $( window ).width() < 768 ) {
			videoSliderFunc();
		}
	} );

	function videoSliderFunc() {
		$( '.most-viewed-section' ).each( function ( index ) {
			const sliderMain = $( this ).find(
				'.most-viewed-section__video-series-wrapper'
			);
			const sliderConfig = {
				infinite: false,
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: true,
				arrows: false,
				autoplay: false,
			};
			if ( videoSliderWrp[ index ] ) {
				videoSliderWrp[ index ].slick( 'refresh' );
			} else {
				videoSliderWrp[ index ] = sliderMain.slick( sliderConfig );
			}
		} );
	}
} )( jQuery );
