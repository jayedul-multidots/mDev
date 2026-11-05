( function ( $ ) {
	'use strict';
	const videoSliderWrp = [];
	$( document ).ready( function () {
		$( window ).on( 'resize', function () {
			videoSliderFunc();
		} );
		videoSliderFunc();
	} );

	function videoSliderFunc() {
		$( '.show-card-slider-section' ).each( function ( index ) {
			const sliderMain = $( this ).find(
				'.show-card-slider-section__video-show-wrapper'
			);
			const autoplay = $( this ).attr( 'data-block-auto-slide' ),
				arrows = $( this ).attr( 'data-display-arrows' );
			const sliderConfig = {
				infinite: false,
				slidesToShow: 3,
				slidesToScroll: 1,
				dots: false,
				autoplay: 'true' === autoplay ? true : false,
				arrows: 'true' === arrows ? true : false,
				responsive: [
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1,
							dots: true,
							arrows: false,
						},
					},
					{
						breakpoint: 577,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							dots: true,
							arrows: false,
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
