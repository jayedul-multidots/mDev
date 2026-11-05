( function ( $ ) {
	'use strict';
	const archiveSeriesSliderWrp = [];
	$( document ).ready( function () {
		archiveSeriesSliderFunc();
		searchSeriesSliderFunc();
		$( window ).on( 'resize', function () {
			archiveSeriesSliderFunc();
			searchSeriesSliderFunc();
		} );

		$( '.archive-series-slider' ).on(
			'setPosition',
			function ( event, slick ) {
				// Remove the class from all slides
				$( '.archive-series-slider .slick-slide' ).removeClass(
					'slider-active-last'
				);

				// Calculate the index of the 6th visible slide based on window width and add class accordingly
				let indexToAddClass;
				const windowWidth = $( window ).width();
				if ( windowWidth < 481 ) {
					indexToAddClass = 1;
				} else if ( windowWidth < 576 ) {
					indexToAddClass = 2;
				} else if ( windowWidth < 768 ) {
					indexToAddClass = 3;
				} else if ( windowWidth < 991 ) {
					indexToAddClass = 4;
				} else {
					indexToAddClass = 5;
				}

				// Add the class to the calculated index
				$( '.archive-series-slider .slick-active' )
					.eq( indexToAddClass )
					.addClass( 'slider-active-last' );
			}
		);
	} );

	function archiveSeriesSliderFunc() {
		$( '.archive-series-slider' ).each( function ( index ) {
			const sliderMain = $( this ).find(
				'.archive-series-slider__slider-section'
			);
			const sliderConfig = {
				slidesToShow: 6,
				slidesToScroll: 1,
				autoplay: false,
				arrows: true,
				dots: false,
				infinite: false,
				responsive: [
					{
						breakpoint: 1025,
						settings: {
							slidesToShow: 5,
							slidesToScroll: 1,
						},
					},
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 4,
							slidesToScroll: 1,
							arrows: false,
							dots: true,
						},
					},
					{
						breakpoint: 576,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 1,
							arrows: false,
							dots: true,
						},
					},
					{
						breakpoint: 481,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1,
							arrows: false,
							dots: true,
						},
					},
				],
			};

			if ( archiveSeriesSliderWrp[ index ] ) {
				archiveSeriesSliderWrp[ index ].slick( 'refresh' );
			} else {
				archiveSeriesSliderWrp[ index ] =
					sliderMain.slick( sliderConfig );
			}
		} );
	}

	function searchSeriesSliderFunc() {
		$( `.search-series-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 4,
				slidesToScroll: 1,
				dots: false,
				arrows: true,
				autoplay: false,
				responsive: [
					{
						breakpoint: 1025,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 1,
						},
					},
					{
						breakpoint: 767,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1,
						},
					},
					{
						breakpoint: 481,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
						},
					},
				],
			} );
	}
} )( jQuery );
