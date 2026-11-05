( function ( $ ) {
	'use strict';
	const videoSliderWrp = [];
	$( document ).ready( function () {
		videoSliderFunc();
		$( window ).on( 'resize', function () {
			videoSliderFunc();
		} );

		$( '.playlist-listing-slider' ).on(
			'setPosition',
			function ( event, slick ) {
				// Remove the class from all slides
				$( '.playlist-listing-slider .slick-slide' ).removeClass(
					'slider-active-last'
				);

				// Calculate the index of the 6th visible slide based on window width and add class accordingly
				let indexToAddClass;
				const windowWidth = $( window ).width();
				if ( windowWidth < 576 ) {
					indexToAddClass = 2;
				} else {
					indexToAddClass = 3;
				}

				if ( indexToAddClass ) {
					$( '.playlist-listing-slider .slick-active' )
						.eq( indexToAddClass )
						.addClass( 'slider-active-last' );
				}
			}
		);
	} );

	function videoSliderFunc() {
		$( '.playlist-listing-slider' ).each( function ( index ) {
			const sliderMain = $( this ).find(
				'.playlist-listing-slider__boxes'
			);
			const autoplay = $( this ).attr( 'data-block-auto-slide' ),
				arrows = $( this ).attr( 'data-display-arrows' );
			const sliderConfig = {
				slidesToShow: 4,
				slidesToScroll: 1,
				autoplay: 'true' === autoplay ? true : false,
				arrows: 'true' === arrows ? true : false,
				dots: false,
				infinite: false,
				responsive: [
					{
						breakpoint: 576,
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
