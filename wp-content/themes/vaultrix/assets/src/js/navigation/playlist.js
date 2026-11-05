/* eslint-disable prefer-const */
/* eslint-disable no-shadow */
/* eslint-disable no-unused-vars */
/* eslint-disable object-shorthand */
/* eslint-disable no-undef */
/* eslint-disable no-alert */
( function () {
	'use strict';

	document.addEventListener(
		'click',
		function ( event ) {
			const button = event.target.closest(
				'.play-from-beginning[data-video-url]'
			);

			if ( ! button ) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			const videoUrl = button.getAttribute( 'data-video-url' );
			const videoType = button.getAttribute( 'data-video-type' ) || '';
			const videoId = button.getAttribute( 'data-video-id' ) || '';

			if ( ! videoUrl ) {
				return;
			}

			closeVideoMiniPopup( button );
			openNativeVideoWatchPopup( videoUrl, videoType, videoId );
		},
		true
	);

	// Video Listing Slider Popup
	jQuery( document ).ready( function ( $ ) {
		const ajaxUrl = siteConfig.ajaxUrl; // eslint-disable-line
		const dataNonce = siteConfig.ajax_nonce; // eslint-disable-line
		let playlistName = '';

		$( document ).on(
			'click',
			'.video-popup-content .popup-modal-close',
			function ( e ) {
				e.preventDefault();
				location.reload();
			}
		);

		// popup modal close button
		$( document ).on(
			'click',
			'.series-popup-content .popup-modal-close',
			function ( e ) {
				e.preventDefault();
				$( 'html' ).removeClass( 'modal-show' );
				$( '.popup-modal' ).removeClass( 'open' );
				$( '.popup-wrapper' ).empty();
			}
		);

		// playlist popup
		// button for create new playlist
		$( document ).on(
			'click',
			'.playlist-popup .create-playlist',
			function ( e ) {
				e.preventDefault();
				$( '.playlist-popup .playlist-input-form' ).show();
			}
		);
		// button for save new playlist
		$( document ).on(
			'click',
			'.playlist-popup .save-playlist',
			function ( e ) {
				e.preventDefault();
				playlistName = $( '.playlist-popup .playlist-name' ).val();
				if ( '' !== playlistName ) {
					$( '.playlist-popup .playlist-note' ).hide();
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_add_playlistname_to_db',
							nonce: dataNonce,
							plname: playlistName,
						},
						success( response ) {
							if ( 'success' === response ) {
								jQuery.ajax( {
									type: 'POST',
									url: ajaxUrl,
									data: {
										action: 'dp_update_playlist',
										nonce: dataNonce,
									},
									success( res ) {
										$(
											'.playlist-popup .popup-playlists'
										).html( res );
										$(
											'.playlist-popup .playlist-name'
										).val( '' );
										$( '.playlist-popup .playlist-note' )
											.text( 'Playlist added !!' )
											.show();
										setTimeout( function () {
											$(
												'.playlist-popup .playlist-note'
											).hide();
										}, 2000 );
									},
								} );
							} else {
								$( '.playlist-popup .playlist-note' )
									.text( response )
									.show();
							}
						},
					} );
				} else {
					$( '.playlist-popup .playlist-note' )
						.text( 'Please enter Playlist name !!' )
						.show();
				}
			}
		);

		// button for save new playlist
		$( document ).on(
			'click',
			'.playlist-popup .playlist-remove',
			function ( e ) {
				e.preventDefault();
				const playlistSlug = $( this ).attr( 'data-playlist-slug' );
				if ( '' !== playlistSlug ) {
					if ( confirm( 'Are you sure to delete this playlist?' ) ) {
						jQuery.ajax( {
							type: 'POST',
							url: ajaxUrl,
							data: {
								action: 'dp_remove_playlist',
								nonce: dataNonce,
								plslug: playlistSlug,
							},
							success( response ) {
								$( '.playlist-popup .popup-playlists' ).html(
									response
								);
							},
						} );
					}
				}
			}
		);
		// button to close the playlist
		$( document ).on(
			'click',
			'.playlist-popup .playlist-popup-close',
			function ( e ) {
				e.preventDefault();
				$( '#playlist-popup' ).hide();
				setTimeout( function () {
					$( '#playlist-popup' ).remove();
				}, 1000 );
				$( '.pl-icon' ).css( 'pointer-events', 'auto' );
			}
		);

		// playlist-popup button when not added to playlist
		$( document ).on(
			'click',
			'.playlist-popup-button .not-added-to-playlist',
			function ( e ) {
				e.preventDefault();
				const element = $( this );
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_playlist_popup',
						nonce: dataNonce,
					},
					success( response ) {
						if ( response ) {
							element.after( response );
							$( '.pl-icon' ).css( 'pointer-events', 'none' );
						}
					},
				} );
			}
		);

		// video-popup
		$( document ).on( 'click', '.video-modal', function ( e ) {
			e.preventDefault();
			const postId = $( this ).attr( 'data-post-id' );
			if ( postId ) {
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_create_video_popup_html',
						nonce: dataNonce,
						pid: postId,
					},
					success( response ) {
						if ( response ) {
							$( 'html' ).addClass( 'modal-show' );
							$( '.popup-modal' ).addClass( 'open' );
							$( '.popup-wrapper' ).scrollTop( 0 );
							$( '.popup-wrapper' ).empty().html( response );
							ymlSlider();
						}
					},
				} );
			}
		} );
		// Playlist popup button for adding video into playlist
		$( document ).on(
			'click',
			'.video-popup-content .btn-add-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( '.video-popup-content' ).attr(
					'data-post-id'
				);
				const playlistSlug = $(
					".playlist-popup input[name='playlist']:checked"
				).val();
				if ( undefined === playlistSlug ) {
					$( '.playlist-popup .playlist-text' ).show();
				}
				if ( undefined !== playlistSlug && '' !== postId ) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_add_item_to_playlist',
							nonce: dataNonce,
							plslug: playlistSlug,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$( '.playlist-popup' ).empty().hide();
								$(
									'.video-popup-content .playlist-popup .playlist-text'
								).hide();
								$(
									'.video-popup-content .playlist-popup-button .pl-icon'
								).removeClass( 'not-added-to-playlist' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon i'
								).removeClass( 'fa-plus' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon'
								).addClass( 'added-to-playlist' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon i'
								).addClass( 'fa-check' );
								$(
									'.video-popup-content .playlist-popup-button .tooltip-text'
								).show();
								$(
									'.video-popup-content .playlist-popup-button .tooltip-text'
								).text( 'Added to Playlist' );
								$( '.video-popup-content .pl-icon' ).css(
									'pointer-events',
									'auto'
								);
								setTimeout( function () {
									$(
										'.video-popup-content .playlist-popup-button .tooltip-text'
									)
										.empty()
										.hide();
								}, 3000 );
							} else {
								$( '.video-popup-content .playlist-text' )
									.text( response )
									.show();
							}
						},
					} );
				}
			}
		);
		// playlist-popup button when video Already in playlist
		$( document ).on(
			'click',
			'.video-popup-content .added-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( '.video-popup-content' ).attr(
					'data-post-id'
				);
				if (
					confirm( 'Are you sure to remove Video from the playlist?' )
				) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_remove_item_from_playlist',
							nonce: dataNonce,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$(
									'.video-popup-content .playlist-popup-button .pl-icon'
								).addClass( 'not-added-to-playlist' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon i'
								).addClass( 'fa-plus' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon'
								).removeClass( 'added-to-playlist' );
								$(
									'.video-popup-content .playlist-popup-button .pl-icon i'
								).removeClass( 'fa-check' );
								$(
									'.video-popup-content .playlist-popup-button .tooltip-text'
								).show();
								$(
									'.video-popup-content .playlist-popup-button .tooltip-text'
								).text( 'Removed from Playlist' );
								setTimeout( function () {
									$(
										'.video-popup-content .playlist-popup-button .tooltip-text'
									)
										.empty()
										.hide();
								}, 2000 );
							}
						},
					} );
				}
			}
		);

		// season click event
		$( document ).on( 'click', '.series-modal', function ( e ) {
			e.preventDefault();
			const seriesId = $( this ).attr( 'data-series-id' );
			if ( seriesId ) {
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_create_series_popup_html',
						nonce: dataNonce,
						series_id: seriesId,
					},
					success( response ) {
						if ( response ) {
							$( 'html' ).addClass( 'modal-show' );
							$( '.popup-modal' ).addClass( 'open' );
							$( '.popup-wrapper' ).scrollTop( 0 );
							$( '.popup-wrapper' ).empty().html( response );

							setTimeout( () => {
								const trailerVideo = $(
									'.series-thumbnail video'
								)[ 0 ];
								const bannerImgElement =
									jQuery( '.series-thumbnail' );
								if ( trailerVideo ) {
									setTimeout( () => {
										bannerImgElement.addClass(
											'video-loading video-mute'
										);
									}, 1000 );
									setTimeout( () => {
										trailerVideo.muted = true; // Mute the video
										trailerVideo.play(); // Attempt to play the video
									}, 2000 );
								}
							}, 200 );

							setTimeout( () => {
								$(
									'.series-seasons .season-tab:first-child'
								).addClass( 'active' );
								tabSliderFunc();
							}, 100 );
						}
					},
				} );
			}
		} );

		$( document ).on( 'click', '.season-episodes .episode', function ( e ) {
			e.preventDefault();
			const trailerVideo = $( '.series-thumbnail video' )[ 0 ];
			if ( trailerVideo ) {
				trailerVideo.pause(); // Attempt to play the video
			}
		} );

		// Playlist popup button for adding season into playlist
		$( document ).on(
			'click',
			'.series-popup-content .btn-add-to-playlist',
			function ( e ) {
				e.preventDefault();
				const seriesId = $( '.series-popup-content' ).attr(
					'data-series-id'
				);
				const playlistSlug = $(
					'.playlist-popup input[name="playlist"]:checked'
				).val();
				if ( undefined === playlistSlug ) {
					$( '.playlist-popup .playlist-text' ).show();
				}
				if ( undefined !== playlistSlug && '' !== seriesId ) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_add_item_to_playlist',
							nonce: dataNonce,
							plslug: playlistSlug,
							sid: seriesId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$( '.playlist-popup' ).empty().hide();
								$(
									'.series-popup-content .playlist-popup .playlist-text'
								).hide();
								$(
									'.series-popup-content .playlist-popup-button .pl-icon'
								).removeClass( 'not-added-to-playlist' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon i'
								).removeClass( 'fa-plus' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon'
								).addClass( 'added-to-playlist' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon i'
								).addClass( 'fa-check' );
								$(
									'.series-popup-content .playlist-popup-button .tooltip-text'
								).show();
								$(
									'.series-popup-content .playlist-popup-button .tooltip-text'
								).text( 'Added to Playlist' );
								$( '.series-popup-content .pl-icon' ).css(
									'pointer-events',
									'auto'
								);
								setTimeout( function () {
									$(
										'.series-popup-content .playlist-popup-button .tooltip-text'
									).hide();
								}, 3000 );
							} else {
								$( '.series-popup-content .playlist-text' )
									.text( response )
									.show();
							}
						},
					} );
				}
			}
		);
		// playlist-popup button when season Already in playlist
		$( document ).on(
			'click',
			'.series-popup-content .added-to-playlist',
			function ( e ) {
				e.preventDefault();
				const seriesId = $( '.series-popup-content' ).attr(
					'data-series-id'
				);
				if (
					confirm(
						'Are you sure to remove Series from the playlist?'
					)
				) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_remove_item_from_playlist',
							nonce: dataNonce,
							sid: seriesId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$(
									'.series-popup-content .playlist-popup-button .pl-icon'
								).addClass( 'not-added-to-playlist' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon i'
								).addClass( 'fa-plus' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon'
								).removeClass( 'added-to-playlist' );
								$(
									'.series-popup-content .playlist-popup-button .pl-icon i'
								).removeClass( 'fa-check' );
								$(
									'.series-popup-content .playlist-popup-button .tooltip-text'
								).show();
								$(
									'.series-popup-content .playlist-popup-button .tooltip-text'
								).text( 'Removed from Playlist' );
								setTimeout( function () {
									$(
										'.series-popup-content .playlist-popup-button .tooltip-text'
									).hide();
								}, 2000 );
							}
						},
					} );
				}
			}
		);
		// on season change
		$( document ).on(
			'click',
			'.series-popup-content .season-tab',
			function ( e ) {
				e.preventDefault();
				const seasonId = $( this ).attr( 'data-season-id' );
				const seriesId = $( this )
					.parents()
					.find( '.series-popup-content' )
					.attr( 'data-series-id' );
				$( '.series-seasons .season-tab' ).removeClass( 'active' );
				$( this ).addClass( 'active' );
				if ( seasonId && seriesId ) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_update_season_list',
							nonce: dataNonce,
							season_id: seasonId,
							series_id: seriesId,
						},
						success( response ) {
							if ( response ) {
								$( '.season-episodes' ).html( response );
							}
						},
					} );
				}
			}
		);

		$( document ).on(
			'click',
			'.video-popup-content .video-buttons .trending-now-btn img',
			function () {
				const videoId = $( '.video-popup-content' ).attr(
					'data-post-id'
				);
				$.ajax( {
					type: 'GET',
					data: {
						action: 'trending_videos_listing',
						video_id: videoId,
					},
					url: siteConfig.ajaxUrl, // eslint-disable-line
					success( response ) {
						$( '.trending-now-btn .tooltip-text' ).show();
						setTimeout( function () {
							$( '.trending-now-btn' ).hide();
						}, 1000 );
						setTimeout( function () {
							$( '.trending-now-btn .tooltip-text' ).hide();
						}, 2000 );
					},
				} );
			}
		);

		// video listing slider playlist events
		$( document ).on(
			'click',
			'.video-listing-hero-banner__slide .btn-add-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( this )
					.parents( '.playlist-popup-button' )
					.attr( 'data-post-id' );
				const element = $( '[data-post-id="' + postId + '"]' );
				const playlistSlug = $(
					".playlist-popup input[name='playlist']:checked"
				).val();
				if ( undefined === playlistSlug ) {
					$( '.playlist-popup .playlist-text' ).show();
				}
				if ( undefined !== playlistSlug && '' !== postId ) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_add_item_to_playlist',
							nonce: dataNonce,
							plslug: playlistSlug,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$( '.playlist-popup' ).empty().hide();
								$( '.playlist-popup .playlist-text' ).hide();
								element
									.find( '.pl-icon' )
									.removeClass( 'not-added-to-playlist' );
								element
									.find( '.pl-icon' )
									.addClass( 'added-to-playlist' );
								element
									.find( '.tooltip-text' )
									.text( 'Added to Playlist' )
									.show();
								$( '.pl-icon' ).css( 'pointer-events', 'auto' );
								setTimeout( function () {
									element
										.find( '.tooltip-text' )
										.empty()
										.hide();
								}, 3000 );

								const slider = jQuery(
									'.video-listing-hero-banner__boxes'
								);
								slider.slick( 'slickPlay' );
								slider.slick(
									'slickSetOption',
									'autoplay',
									true
								);
								jQuery(
									'.video-listing-hero-banner__pause-btn'
								).removeClass( 'paused' );
							} else {
								$( '.playlist-text' ).text( response ).show();
							}
						},
					} );
				}
			}
		);
		// playlist-popup button when video Already in playlist
		$( document ).on(
			'click',
			'.video-listing-hero-banner__slide .added-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( this )
					.parents( '.playlist-popup-button' )
					.attr( 'data-post-id' );
				const element = $( '[data-post-id="' + postId + '"]' );
				if (
					confirm( 'Are you sure to remove Video from the playlist?' )
				) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_remove_item_from_playlist',
							nonce: dataNonce,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								element
									.find( '.pl-icon' )
									.addClass( 'not-added-to-playlist' );
								element
									.find( '.pl-icon' )
									.removeClass( 'added-to-playlist' );
								element
									.find( '.tooltip-text' )
									.text( 'Removed from Playlist' )
									.show();
								setTimeout( function () {
									element.find( '.tooltip-text' ).hide();
								}, 2000 );
								// videoListingHeroBanner();

								const slider = jQuery(
									'.video-listing-hero-banner__boxes'
								);
								slider.slick( 'slickPlay' );
								slider.slick(
									'slickSetOption',
									'autoplay',
									true
								);
								jQuery(
									'.video-listing-hero-banner__pause-btn'
								).removeClass( 'paused' );
							}
						},
					} );
				}
			}
		);

		$( window ).on( 'resize', function () {
			ymlSlider();
		} );

		// Video play popup js — 2026-07-01: routes AJAX watch popup to native video init (see initWatchVideoPopup).
		$( document ).on( 'click', '.v-watch-now', function ( e ) {
			e.preventDefault();
			const postId = $( this ).attr( 'data-post-id' );
			if ( postId ) {
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_create_watch_video_html',
						nonce: dataNonce,
						pid: postId,
					},
					success( response ) {
						if ( response ) {
							$( 'html' ).addClass( 'video-watch-popup-show' );
							$( '.video-watch-popup' ).addClass( 'open' );
							$( '.video-watch-popup' ).empty().html( response );
							initWatchVideoPopup( false );
						}
					},
				} );
			}
		} );
		$( document ).on( 'click', '.play-from-beginning', function ( e ) {
			if ( $( this ).attr( 'data-video-url' ) ) {
				return;
			}

			e.preventDefault();
			const postId = $( this ).attr( 'data-post-id' );
			if ( postId ) {
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_create_watch_video_html',
						nonce: dataNonce,
						pid: postId,
					},
					success( response ) {
						if ( response ) {
							$( 'html' ).addClass( 'video-watch-popup-show' );
							$( '.video-watch-popup' ).addClass( 'open' );
							$( '.video-watch-popup' ).empty().html( response );
							initWatchVideoPopup( true );
						}
					},
				} );
			}
		} );

		$( document ).on( 'click', '.watch-video-back-button', function ( e ) {
			e.preventDefault();

			const nativeVideo = $( '.video-watch-popup video' ).get( 0 );

			if ( nativeVideo && nativeVideo.getAttribute( 'data-video-id' ) ) {
				saveNativeVideoProgress( nativeVideo );
			}

			$( 'html' ).removeClass( 'video-watch-popup-show' );
			$( '.video-watch-popup' ).removeClass( 'open' );
			$( '.video-watch-popup' ).empty();
			if ( $( 'body' ).hasClass( 'archive' ) ) {
				const bannerVideo = $( '.archive-banner__media video' )[ 0 ];
				// Check if the video element is found
				if ( bannerVideo ) {
					// Pause the video
					bannerVideo.play();
				}
			}
		} );

		// Close popups when clicked outside the the popup
		document.addEventListener( 'click', function ( event ) {
			const playlistPopup = document.querySelector(
				'.popup-modal #playlist-popup'
			);
			if ( playlistPopup && ! playlistPopup.contains( event.target ) ) {
				playlistPopup.remove();
				$( '.pl-icon' ).css( 'pointer-events', 'auto' );
			}
		} );

		// button to watch series trailer
		$( document ).on( 'click', '.v-watch-trailer', function ( e ) {
			e.preventDefault();
			const trailerUrl = $( this ).attr( 'data-trailer-url' );
			const trailerType = $( this ).attr( 'data-trailer-type' ) || '';

			if ( trailerUrl ) {
				const typeAttr = trailerType ? ` type="${ trailerType }"` : '';
				const response = `
					<div class="watch-video-loader" id="watch-video-loader"></div>
					<div class="watch-video-content watch-trailer-content" id="watch-video-content">
						<div class="watch-trailer-back-button"></div>
						<video controls playsinline preload="metadata">
							<source src="${ trailerUrl }"${ typeAttr }>
						</video>
					</div>
				`;

				$( 'html' ).addClass( 'video-video-popup-show' );
				$( '.video-watch-popup' ).addClass( 'open' );
				$( '.video-watch-popup' ).empty().html( response );

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

				return;
			}

			const trailerID = $( this ).attr( 'data-trailer-id' );
			if ( trailerID ) {
				jQuery.ajax( {
					type: 'POST',
					url: ajaxUrl,
					data: {
						action: 'dp_create_series_trailer_html',
						nonce: dataNonce,
						trailer_url: trailerID,
					},
					success( response ) {
						if ( response ) {
							$( 'html' ).addClass( 'video-video-popup-show' );
							$( '.video-watch-popup' ).addClass( 'open' );
							$( '.video-watch-popup' ).empty().html( response );
							setTimeout( () => {
								$(
									'.video-watch-popup .watch-video-loader'
								).remove();
							}, 500 );
							setTimeout( () => {
								const video = $(
									'.watch-trailer-content video'
								).get( 0 );

								if ( video ) {
									const playPromise = video.play();

									if ( playPromise !== undefined ) {
										playPromise.catch( () => {} );
									}
								}
							}, 600 );
						}
					},
				} );
			}
		} );

		$( document ).on(
			'click',
			'.watch-trailer-back-button',
			function ( e ) {
				e.preventDefault();
				$( 'html' ).removeClass( 'video-video-popup-show' );
				$( '.video-watch-popup' ).removeClass( 'open' );
				$( '.video-watch-popup' ).empty();
			}
		);

		$( document ).on(
			'click',
			'.video-hover-popup .video-mini-popup-close',
			function () {
				$( this )
					.parents()
					.find( '.video-popup-wrapper' )
					.removeClass( 'video-mini-popup-show' );
				$( 'html' ).removeClass( 'html-video-mini-popup-show' );
				$( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.removeClass( 'video-popup-open' );
			}
		);

		// video listing slider playlist events
		$( document ).on(
			'click',
			'.video-hover-popup .btn-add-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( this )
					.parents( '.playlist-popup-button' )
					.attr( 'data-post-id' );
				const element = $( this ).parents( '.playlist-popup-button' );
				const playlistSlug = $(
					".playlist-popup input[name='playlist']:checked"
				).val();
				if ( undefined === playlistSlug ) {
					$( '.playlist-popup .playlist-text' ).show();
				}
				if ( undefined !== playlistSlug && '' !== postId ) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_add_item_to_playlist',
							nonce: dataNonce,
							plslug: playlistSlug,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								$( '.playlist-popup' ).empty().hide();
								$( '.playlist-popup .playlist-text' ).hide();
								element
									.find( '.pl-icon' )
									.removeClass( 'not-added-to-playlist' );
								element
									.find( '.pl-icon' )
									.addClass( 'added-to-playlist' );
								element.find( '.tooltip-text' ).empty();
								element
									.find( '.tooltip-text' )
									.empty()
									.text( 'Added to Playlist' )
									.show();
								$( '.pl-icon' ).css( 'pointer-events', 'auto' );
								setTimeout( function () {
									element
										.find( '.tooltip-text' )
										.empty()
										.hide();
								}, 3000 );
							} else {
								$( '.playlist-text' ).text( response ).show();
							}
						},
					} );
				}
			}
		);

		// playlist-popup button when video Already in playlist
		$( document ).on(
			'click',
			'.video-hover-popup .added-to-playlist',
			function ( e ) {
				e.preventDefault();
				const postId = $( this )
					.parents( '.playlist-popup-button' )
					.attr( 'data-post-id' );
				const element = $( '[data-post-id="' + postId + '"]' );
				if (
					confirm( 'Are you sure to remove Video from the playlist?' )
				) {
					jQuery.ajax( {
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'dp_remove_item_from_playlist',
							nonce: dataNonce,
							pid: postId,
						},
						success( response ) {
							if ( 'success' === response ) {
								element
									.find( '.pl-icon' )
									.addClass( 'not-added-to-playlist' );
								element
									.find( '.pl-icon' )
									.removeClass( 'added-to-playlist' );
								element
									.find(
										'.playlist-popup-button .tooltip-text'
									)
									.text( 'Removed from Playlist' )
									.show();
								setTimeout( function () {
									element.find( '.tooltip-text' ).hide();
								}, 2000 );
								// videoListingHeroBanner();

								const slider = jQuery(
									'.video-listing-hero-banner__boxes'
								);
								slider.slick( 'slickPlay' );
								slider.slick(
									'slickSetOption',
									'autoplay',
									true
								);
								jQuery(
									'.video-listing-hero-banner__pause-btn'
								).removeClass( 'paused' );
							}
						},
					} );
				}
			}
		);
	} );
} )( jQuery );

jQuery( document ).ready( function ( $ ) {
	const bannerVideo = jQuery( '.archive-banner__media video' )[ 0 ];
	const bannerImgElement = jQuery( '.archive-banner__media' );
	if ( bannerVideo ) {
		setTimeout( () => {
			bannerImgElement.addClass( 'video-loading' );
			$( '.archive-banner__media' ).addClass( 'video-mute' );
		}, 1000 );
		setTimeout( () => {
			bannerVideo.muted = true; // Mute the video
			bannerVideo.play(); // Attempt to play the video
		}, 2000 );
	}

	const windowWidth = $( window ).width();
	if ( windowWidth > 767 ) {
		jQuery( document ).on(
			'mouseenter',
			'.video-popup-wrapper',
			function ( e ) {
				const video = this.querySelector( 'video' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				$( this ).addClass( 'video-mini-popup-show' );
				$( 'html' ).addClass( 'html-video-mini-popup-show' );
				$( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.addClass( 'video-popup-open' );
				$( this ).find( '.video-hover-popup' ).addClass( 'video-mute' );
				if ( video ) {
					video.muted = true; // Mute
					setTimeout( () => {
						video.play();
					}, 1000 );

					if ( bannerVideo ) {
						bannerVideo.pause();
					}
				}
			}
		);

		jQuery( document ).on(
			'mouseleave',
			'.video-popup-wrapper',
			function ( e ) {
				const video = this.querySelector( 'video' );
				$( this ).removeClass( 'video-mini-popup-show' );
				$( 'html' ).removeClass( 'html-video-mini-popup-show' );
				$( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.removeClass( 'video-popup-open' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				if ( video ) {
					video.pause();
				}
				$( '#playlist-popup' ).hide();
				setTimeout( function () {
					$( '#playlist-popup' ).remove();
				}, 1000 );
				$( '.pl-icon' ).css( 'pointer-events', 'auto' );
				if ( bannerVideo ) {
					bannerVideo.muted = true;
					bannerVideo.play();
				}
			}
		);
	} else {
		$( document ).on( 'click', '.video-popup-wrapper', function () {
			const video = this.querySelector( 'video' );
			const bannerVideo = jQuery( '.archive-banner__media video' )[ 0 ];
			$( this ).addClass( 'video-mini-popup-show' );
			$( 'html' ).addClass( 'html-video-mini-popup-show' );
			$( this )
				.parents( '.video-popup-wrapper-with-slider' )
				.addClass( 'video-popup-open' );
			$( this ).find( '.video-hover-popup' ).addClass( 'video-mute' );
			if ( video ) {
				video.muted = true; // Mute
				setTimeout( () => {
					video.play();
				}, 1000 );

				if ( bannerVideo ) {
					bannerVideo.pause();
				}
			}
		} );

		$( document ).on(
			'click',
			'.video-popup-wrapper .video-mini-popup-close',
			function () {
				// e.preventDefault();
				const video = $( this )
					.parents( '.video-hover-popup' )
					.find( 'video' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				$( this )
					.parents( '.video-hover-popup' )
					.removeClass( 'video-mini-popup-show' );
				$( 'html' ).removeClass( 'html-video-mini-popup-show' );
				$( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.removeClass( 'video-popup-open' );
				if ( video ) {
					video.pause();
				}
				$( '#playlist-popup' ).hide();
				setTimeout( function () {
					$( '#playlist-popup' ).remove();
				}, 1000 );
				$( '.pl-icon' ).css( 'pointer-events', 'auto' );
				if ( bannerVideo ) {
					bannerVideo.muted = true;
					bannerVideo.play();
				}
			}
		);
	}

	$( document ).on(
		'click',
		'.vaultrix-archive-main .play-from-beginning',
		function ( e ) {
			e.preventDefault();
			const bannerVideo = jQuery( '.archive-banner__media video' )[ 0 ];
			bannerVideo.muted = true; // Mute the video
			bannerVideo.pause();
			$( '.archive-banner__media' ).addClass( 'video-mute' );
		}
	);
	$( document ).on(
		'click',
		'.video-hover-popup .video-buttons .trending-now-btn img',
		function () {
			const videoId = $( this )
				.parents( '.video-hover-popup' )
				.attr( 'data-post-id' );
			let _this = jQuery( this );

			$.ajax( {
				type: 'GET',
				data: {
					action: 'trending_videos_listing',
					video_id: videoId,
				},
				url: siteConfig.ajaxUrl, // eslint-disable-line
				success( response ) {
					_this
						.parents( '.trending-now-btn' )
						.find( '.tooltip-text' )
						.show();
					setTimeout( function () {
						_this.parents( '.trending-now-btn' ).hide();
					}, 1000 );
					setTimeout( function () {
						_this
							.parents( '.trending-now-btn' )
							.find( '.tooltip-text' )
							.hide();
					}, 2000 );
				},
			} );
		}
	);

	$( document ).on(
		'click',
		'.archive-banner__media .mute-button',
		function ( e ) {
			e.preventDefault();
			if ( bannerVideo.muted ) {
				bannerVideo.muted = false; // Mute the video
				$( '.archive-banner__media' ).removeClass( 'video-mute' );
			} else {
				bannerVideo.muted = true; // Mute the video
				$( '.archive-banner__media' ).addClass( 'video-mute' );
			}
		}
	);

	$( document ).on(
		'click',
		'.video-hover-popup .mute-button',
		function ( e ) {
			e.preventDefault();
			const video = $( this )
				.parents( '.video-hover-popup' )
				.find( 'video' )[ 0 ];

			if ( video.muted ) {
				video.muted = false; // Mute the video
				$( this )
					.parents( '.video-hover-popup' )
					.removeClass( 'video-mute' );
			} else {
				video.muted = true; // Mute the video
				$( this )
					.parents( '.video-hover-popup' )
					.addClass( 'video-mute' );
			}
		}
	);
} );

jQuery( window ).resize( function ( $ ) {
	const windowWidth = jQuery( window ).width();
	if ( windowWidth > 767 ) {
		jQuery( document ).on(
			'mouseenter',
			'.video-popup-wrapper',
			function ( e ) {
				const video = this.querySelector( 'video' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				jQuery( this ).addClass( 'video-mini-popup-show' );
				jQuery( 'html' ).addClass( 'html-video-mini-popup-show' );
				jQuery( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.addClass( 'video-popup-open' );
				jQuery( this )
					.find( '.video-hover-popup' )
					.addClass( 'video-mute' );
				if ( video ) {
					video.muted = true; // Mute
					setTimeout( () => {
						video.play();
					}, 1000 );

					if ( bannerVideo ) {
						bannerVideo.pause();
					}
				}
			}
		);

		jQuery( document ).on(
			'mouseleave',
			'.video-popup-wrapper',
			function () {
				const video = this.querySelector( 'video' );
				jQuery( this ).removeClass( 'video-mini-popup-show' );
				jQuery( 'html' ).removeClass( 'html-video-mini-popup-show' );
				jQuery( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.removeClass( 'video-popup-open' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				if ( video ) {
					video.pause();
				}
				jQuery( '#playlist-popup' ).hide();
				setTimeout( function () {
					jQuery( '#playlist-popup' ).remove();
				}, 100 );
				jQuery( '.pl-icon' ).css( 'pointer-events', 'auto' );
				if ( bannerVideo ) {
					bannerVideo.muted = true;
					bannerVideo.play();
				}
			}
		);
	} else {
		jQuery( document ).on( 'click', '.video-popup-wrapper', function () {
			const video = this.querySelector( 'video' );
			const bannerVideo = jQuery( '.archive-banner__media video' )[ 0 ];
			jQuery( this ).addClass( 'video-mini-popup-show' );
			jQuery( 'html' ).addClass( 'html-video-mini-popup-show' );
			jQuery( this )
				.parents( '.video-popup-wrapper-with-slider' )
				.addClass( 'video-popup-open' );
			jQuery( this )
				.find( '.video-hover-popup' )
				.addClass( 'video-mute' );
			if ( video ) {
				video.muted = true; // Mute
				setTimeout( () => {
					video.play();
				}, 1000 );

				if ( bannerVideo ) {
					bannerVideo.pause();
				}
			}
		} );

		jQuery( document ).on(
			'click',
			'.video-popup-wrapper .video-mini-popup-close',
			function ( e ) {
				e.preventDefault();
				const video = jQuery( this )
					.parents( '.video-hover-popup' )
					.find( 'video' );
				const bannerVideo = jQuery(
					'.archive-banner__media video'
				)[ 0 ];
				jQuery( this )
					.parents( '.video-hover-popup' )
					.removeClass( 'video-mini-popup-show' );
				jQuery( 'html' ).removeClass( 'html-video-mini-popup-show' );
				jQuery( this )
					.parents( '.video-popup-wrapper-with-slider' )
					.removeClass( 'video-popup-open' );
				if ( video ) {
					video.pause();
				}
				jQuery( '#playlist-popup' ).hide();
				setTimeout( function () {
					jQuery( '#playlist-popup' ).remove();
				}, 1000 );
				jQuery( '.pl-icon' ).css( 'pointer-events', 'auto' );
				if ( bannerVideo ) {
					bannerVideo.muted = true;
					bannerVideo.play();
				}
			}
		);
	}
} );
function ymlSlider() {
	const sliderMain = jQuery( '.yml-content' );
	const slickConfig = {
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		arrows: true,
		dots: false,
		infinite: true,
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
				breakpoint: 481,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: true,
					arrows: false,
				},
			},
		],
	};
	if ( ! sliderMain.hasClass( 'slick-initialized' ) ) {
		// If not initialized, initialize Slick slider
		sliderMain.slick( slickConfig );
	}
}
function tabSliderFunc() {
	const tabSliderMain = jQuery( '.series-popup-content .season-tab-wrapper' );
	const sliderConfig = {
		infinite: false,
		slidesToShow: 5,
		slidesToScroll: 1,
		dots: false,
		arrows: true,
		autoplay: false,
		variableWidth: true,
		responsive: [
			{
				breakpoint: 1025,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 481,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
				},
			},
		],
	};

	tabSliderMain.slick( sliderConfig );

	if ( ! tabSliderMain.hasClass( 'slick-initialized' ) ) {
		// If not initialized, initialize Slick slider
		tabSliderMain.slick( sliderConfig );
	}
}
function continueWatch( isPlayFromBeginning ) {
	const videos = document.querySelectorAll( '.bc-player-default_default' );
	const dataNonce = siteConfig.ajax_nonce; // eslint-disable-line
	videos.forEach( ( video, index ) => {
		const player = videojs( video );
		player.addClass( 'vjs-has-started' );

		const videoID = video.getAttribute( 'data-video-id' );
		const totalDuration = player.duration();
		setVideoDurationTime( videoID, totalDuration );

		// +++ Create divs for buttons +++
		let controlBar,
			newElementPB = document.createElement( 'div' ),
			newElementTitle = document.createElement( 'div' ),
			newElementNB = document.createElement( 'div' );

		const customControlsContainer = document.createElement( 'div' );
		customControlsContainer.id = 'customVideoControls';

		newElementPB.id = 'previousButton';
		newElementTitle.id = 'videoTitle';
		newElementNB.id = 'nextButton';

		controlBar = player.$( '.vjs-control-bar' );

		// Insert the Controls in proper location
		customControlsContainer.appendChild( newElementPB );
		customControlsContainer.appendChild( newElementTitle );
		customControlsContainer.appendChild( newElementNB );
		controlBar.insertBefore(
			customControlsContainer,
			player.$( '.vjs-current-time' )
		);

		const videoIds = [];
		const videoTitles = [];
		const videoCats = [];
		const videoParentShow = video.getAttribute( 'data-parent-show' );
		const videoChildShow = video.getAttribute( 'data-child-show' );
		const videoParentSeason = video.getAttribute( 'data-parent-season' );
		const videoChildSeason = video.getAttribute( 'data-child-season' );
		const currentVideoID = video.getAttribute( 'data-video-id' );
		const videoEpisode = video.getAttribute( 'data-season-episode' );
		const videoPostID = video.getAttribute( 'data-video-post-id' );
		const fetchURL =
			siteConfig.siteUrl +
			'/wp-json/wp/v2/bcvideolist?video_parent_show=' +
			videoParentShow +
			'&video_child_show=' +
			videoChildShow +
			'&video_current_id=' +
			currentVideoID +
			'&video_parent_season=' +
			videoParentSeason +
			'&video_child_season=' +
			videoChildSeason +
			'&video_season_episode=' +
			videoEpisode +
			'&video_post_id=' +
			videoPostID;
		fetch( fetchURL )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				data.forEach( ( video, index ) => {
					videoIds.push( video.id );
					videoTitles.push( video.title );
					videoCats.push( video.cat );
				} );
				return videoIds;
			} );

		let currentVideoIndex;
		if ( videoEpisode && videoParentShow == 'series' ) {
			currentVideoIndex = videoEpisode - 1;
		} else {
			currentVideoIndex = 0;
		}

		let totalDurationElement = '';
		const prevButton = video.querySelector( '#previousButton' );
		const nextButton = video.querySelector( '#nextButton' );
		const categoryParagraph = document.createElement( 'h3' );
		categoryParagraph.id = 'videoCategory';
		newElementTitle.appendChild( categoryParagraph );
		const titleHeading = document.createElement( 'p' );
		newElementTitle.appendChild( titleHeading );
		player.ready( function () {
			setTimeout( () => {
				updateVideoTitle( currentVideoIndex );
			}, 800 );
		} );
		function updateVideoTitle( index ) {
			if ( index >= 0 && index < videoTitles.length ) {
				const title = videoTitles[ index ];
				const category = videoCats[ index ];
				if ( title ) {
					titleHeading.innerText = title;
					categoryParagraph.innerText = category + ' ' + ': ';
				}
			}
		}
		//tooltip
		prevButton.addEventListener( 'mouseover', ( event ) =>
			displayTitle( event, currentVideoIndex - 1 )
		);
		prevButton.addEventListener( 'mouseout', ( event ) =>
			hideTitle( event )
		);
		nextButton.addEventListener( 'mouseover', ( event ) =>
			displayTitle( event, currentVideoIndex + 1 )
		);
		nextButton.addEventListener( 'mouseout', ( event ) =>
			hideTitle( event )
		);
		function displayTitle( event, index ) {
			// Check if index is within valid range
			if ( index >= 0 && index < videoTitles.length ) {
				const title = videoTitles[ index ];
				if ( title ) {
					event.target.setAttribute( 'data-tooltip', title );
				}
			}
		}
		function hideTitle( event ) {
			// Hide the tooltip
			event.target.removeAttribute( 'data-tooltip' );
		}

		setTimeout( updateButtons, 500 );
		function updateButtons() {
			if ( currentVideoIndex === 0 ) {
				prevButton.setAttribute( 'disabled', 'disabled' );
			} else {
				prevButton.removeAttribute( 'disabled' );
			}

			if ( currentVideoIndex === videoIds.length - 1 ) {
				nextButton.setAttribute( 'disabled', 'disabled' );
			} else {
				nextButton.removeAttribute( 'disabled' );
			}
		}

		// Add event listeners for previous and next buttons or any other triggers
		prevButton.addEventListener( 'click', playPreviousVideo );
		nextButton.addEventListener( 'click', playNextVideo );

		function playPreviousVideo() {
			if ( videoIds.length ) {
				if ( currentVideoIndex > 0 ) {
					currentVideoIndex--;
					player.catalog.getVideo(
						videoIds[ currentVideoIndex ],
						function ( error, video ) {
							if ( ! error ) {
								player.catalog.load( video );
								updateVideoTitle( currentVideoIndex );
								player.play();
							}
						}
					);
					updateButtons();
				}
			}
			if ( currentVideoIndex > 0 ) {
				prevButton.removeAttribute( 'disabled' );
			}

			if ( currentVideoIndex < videoIds.length - 1 ) {
				nextButton.removeAttribute( 'disabled' );
			}
		}
		function playNextVideo() {
			if ( videoIds.length ) {
				if ( currentVideoIndex < videoIds.length - 1 ) {
					currentVideoIndex++;
					player.catalog.getVideo(
						videoIds[ currentVideoIndex ],
						function ( error, video ) {
							if ( ! error ) {
								player.catalog.load( video );
								updateVideoTitle( currentVideoIndex );
								player.play();
							}
						}
					);
					updateButtons();
				}
			}
		}

		//continue watching functionality
		if ( ! isPlayFromBeginning && player.playing !== true ) {
			const videoID = video.getAttribute( 'data-video-id' );
			const str =
				'&videoID=' +
				videoID +
				'&action=brightcove_video_get_time' +
				'&nonce=' +
				dataNonce;
			jQuery.ajax( {
				type: 'POST',
				dataType: 'json',
				url: siteConfig.ajaxUrl,
				data: str,
				success: function ( res ) {
					if ( res !== null ) {
						player.currentTime( parseFloat( res ) );
					}
				},
			} );
		}

		player.on( 'pause', () => {
			const videoID = video.getAttribute( 'data-video-id' );
			let currentVideoTime = player.currentTime();
			const totalDuration = player.duration();
			if ( currentVideoTime === totalDuration ) {
				currentVideoTime = '';
			}
			setWatchedTime( videoID, currentVideoTime, totalDuration );
		} );

		window.addEventListener( 'beforeunload', () => {
			const videoID = video.getAttribute( 'data-video-id' );
			let currentVideoTime = player.currentTime();
			const totalDuration = player.duration();
			if ( currentVideoTime === totalDuration ) {
				currentVideoTime = '';
			}
			setWatchedTime( videoID, currentVideoTime, totalDuration );
		} );

		jQuery( document ).on(
			'click',
			'.watch-video-back-button',
			function ( e ) {
				const videoID = video.getAttribute( 'data-video-id' );
				let currentVideoTime = player.currentTime();
				const totalDuration = player.duration();
				if ( currentVideoTime === totalDuration ) {
					currentVideoTime = '';
				}
				setWatchedTime( videoID, currentVideoTime, totalDuration );
			}
		);
	} );
}
// 2026-07-01: Picks native vs legacy Brightcove init after dp_create_watch_video_html AJAX.
function initWatchVideoPopup( isPlayFromBeginning ) {
	const nativeVideo = document.querySelector(
		'.video-watch-popup .watch-video-content video'
	);

	if ( nativeVideo ) {
		initNativeWatchVideoPopup( isPlayFromBeginning );
		return;
	}

	setTimeout( () => {
		continueWatch( isPlayFromBeginning );
	}, 2000 );
	setTimeout( () => {
		jQuery( '.video-watch-popup .watch-video-loader' ).remove();
	}, 2800 );
	setTimeout( () => {
		jQuery( '.video-watch-popup video-js video' ).trigger( 'play' );
	}, 3000 );
}
// 2026-07-01: Plays ACF main_video in watch popup — resume, progress save, and autoplay.
function initNativeWatchVideoPopup( isPlayFromBeginning ) {
	setTimeout( () => {
		jQuery( '.video-watch-popup .watch-video-loader' ).remove();

		const video = jQuery( '.video-watch-popup video' ).get( 0 );

		if ( ! video ) {
			return;
		}

		setupNativeVideoProgressTracking( video );
		setupNativeWatchVideoNavigation( video );

		video.addEventListener(
			'loadedmetadata',
			() => {
				const videoID = video.getAttribute( 'data-video-id' );

				if ( videoID && video.duration ) {
					setVideoDurationTime( videoID, video.duration );
				}
			},
			{ once: true }
		);

		const startPlayback = () => {
			const playPromise = video.play();

			if ( playPromise !== undefined ) {
				playPromise.catch( () => {} );
			}
		};

		if ( isPlayFromBeginning ) {
			const playFromStart = () => {
				video.currentTime = 0;
				startPlayback();
			};

			if ( video.readyState >= 1 ) {
				playFromStart();
			} else {
				video.addEventListener( 'loadedmetadata', playFromStart, {
					once: true,
				} );
			}
			return;
		}

		resumeNativeVideoFromSavedTime( video, startPlayback );
	}, 500 );
}
// 2026-07-01: Prev/next episode controls for native video via bcvideolist API.
function setupNativeWatchVideoNavigation( video ) {
	const watchVideoContent = video.closest( '.watch-video-content' );

	if ( ! watchVideoContent || watchVideoContent.querySelector( '#customVideoControls' ) ) {
		return;
	}

	const customControlsContainer = document.createElement( 'div' );
	const newElementPB = document.createElement( 'div' );
	const newElementTitle = document.createElement( 'div' );
	const newElementNB = document.createElement( 'div' );

	customControlsContainer.id = 'customVideoControls';
	newElementPB.id = 'previousButton';
	newElementTitle.id = 'videoTitle';
	newElementNB.id = 'nextButton';

	customControlsContainer.appendChild( newElementPB );
	customControlsContainer.appendChild( newElementTitle );
	customControlsContainer.appendChild( newElementNB );
	watchVideoContent.appendChild( customControlsContainer );

	const videoIds = [];
	const videoTitles = [];
	const videoCats = [];
	const videoSources = [];
	const videoParentShow = video.getAttribute( 'data-parent-show' );
	const videoChildShow = video.getAttribute( 'data-child-show' );
	const videoParentSeason = video.getAttribute( 'data-parent-season' );
	const videoChildSeason = video.getAttribute( 'data-child-season' );
	const currentVideoID = video.getAttribute( 'data-video-id' );
	const videoEpisode = video.getAttribute( 'data-season-episode' );
	const videoPostID = video.getAttribute( 'data-video-post-id' );
	const fetchURL =
		siteConfig.siteUrl +
		'/wp-json/wp/v2/bcvideolist?video_parent_show=' +
		videoParentShow +
		'&video_child_show=' +
		videoChildShow +
		'&video_current_id=' +
		currentVideoID +
		'&video_parent_season=' +
		videoParentSeason +
		'&video_child_season=' +
		videoChildSeason +
		'&video_season_episode=' +
		videoEpisode +
		'&video_post_id=' +
		videoPostID;

	let currentVideoIndex = 0;

	if ( videoEpisode && videoParentShow === 'financial-masterclass' ) {
		currentVideoIndex = videoEpisode - 1;
	}

	const prevButton = newElementPB;
	const nextButton = newElementNB;
	const categoryParagraph = document.createElement( 'h3' );
	categoryParagraph.id = 'videoCategory';
	newElementTitle.appendChild( categoryParagraph );
	const titleHeading = document.createElement( 'p' );
	newElementTitle.appendChild( titleHeading );

	const updateVideoTitle = ( index ) => {
		if ( index >= 0 && index < videoTitles.length ) {
			const title = videoTitles[ index ];
			const category = videoCats[ index ];

			if ( title ) {
				titleHeading.innerText = title;
				categoryParagraph.innerText = category + ' ' + ': ';
			}
		}
	};

	const updateButtons = () => {
		if ( currentVideoIndex === 0 ) {
			prevButton.setAttribute( 'disabled', 'disabled' );
		} else {
			prevButton.removeAttribute( 'disabled' );
		}

		if ( currentVideoIndex === videoIds.length - 1 ) {
			nextButton.setAttribute( 'disabled', 'disabled' );
		} else {
			nextButton.removeAttribute( 'disabled' );
		}
	};

	const loadVideoAtIndex = ( index, playFromBeginning = false ) => {
		const sourceData = videoSources[ index ];

		if ( ! sourceData || ! sourceData.url ) {
			return;
		}

		saveNativeVideoProgress( video );

		video.setAttribute( 'data-video-id', sourceData.id );
		video.setAttribute( 'data-video-post-id', sourceData.post_id || '' );

		const source = video.querySelector( 'source' );

		if ( source ) {
			source.src = sourceData.url;

			if ( sourceData.type ) {
				source.type = sourceData.type;
			}
		}

		video.load();
		currentVideoIndex = index;
		updateVideoTitle( currentVideoIndex );
		updateButtons();

		const startPlayback = () => {
			const playPromise = video.play();

			if ( playPromise !== undefined ) {
				playPromise.catch( () => {} );
			}
		};

		if ( playFromBeginning ) {
			const playFromStart = () => {
				video.currentTime = 0;
				startPlayback();
			};

			if ( video.readyState >= 1 ) {
				playFromStart();
			} else {
				video.addEventListener( 'loadedmetadata', playFromStart, {
					once: true,
				} );
			}
			return;
		}

		resumeNativeVideoFromSavedTime( video, startPlayback );
	};

	const playPreviousVideo = () => {
		if ( currentVideoIndex > 0 ) {
			loadVideoAtIndex( currentVideoIndex - 1 );
		}
	};

	const playNextVideo = () => {
		if ( currentVideoIndex < videoIds.length - 1 ) {
			loadVideoAtIndex( currentVideoIndex + 1 );
		}
	};

	prevButton.addEventListener( 'click', playPreviousVideo );
	nextButton.addEventListener( 'click', playNextVideo );

	fetch( fetchURL )
		.then( ( response ) => response.json() )
		.then( ( data ) => {
			data.forEach( ( item ) => {
				videoIds.push( item.id );
				videoTitles.push( item.title );
				videoCats.push( item.cat );
				videoSources.push( {
					id: item.id,
					url: item.url,
					type: item.type,
					post_id: item.post_id,
				} );
			} );

			if ( videoEpisode && videoParentShow === 'series' ) {
				currentVideoIndex = videoEpisode - 1;
			} else {
				currentVideoIndex = videoIds.indexOf( currentVideoID );

				if ( currentVideoIndex < 0 ) {
					currentVideoIndex = 0;
				}
			}

			setTimeout( () => {
				updateVideoTitle( currentVideoIndex );
				updateButtons();
			}, 500 );
		} );
}
function buildNativeVideoWatchPopupHtml( url, type, videoId ) {
	const typeAttr = type ? ` type="${ type }"` : '';
	const videoIdAttr = videoId ? ` data-video-id="${ videoId }"` : '';

	return `
		<div class="watch-video-loader" id="watch-video-loader"></div>
		<div class="watch-video-content" id="watch-video-content">
			<div class="watch-video-back-button"></div>
			<video controls playsinline preload="metadata"${ videoIdAttr }>
				<source src="${ url }"${ typeAttr }>
			</video>
		</div>
	`;
}

function saveNativeVideoProgress( video ) {
	if ( ! video ) {
		return;
	}

	const videoID = video.getAttribute( 'data-video-id' );

	if ( ! videoID ) {
		return;
	}

	let currentVideoTime = video.currentTime;
	const totalDuration = video.duration;

	if ( currentVideoTime === totalDuration ) {
		currentVideoTime = '';
	}

	setWatchedTime( videoID, currentVideoTime, totalDuration );
}

function setupNativeVideoProgressTracking( video ) {
	if ( ! video ) {
		return;
	}

	const onProgressSave = () => saveNativeVideoProgress( video );

	video.addEventListener( 'pause', onProgressSave );
	window.addEventListener( 'beforeunload', onProgressSave );
}

function resumeNativeVideoFromSavedTime( video, onReady ) {
	const videoID = video.getAttribute( 'data-video-id' );

	if ( ! videoID || typeof siteConfig === 'undefined' ) {
		if ( onReady ) {
			onReady();
		}
		return;
	}

	const startPlayback = ( seekTo ) => {
		const playVideo = () => {
			if ( onReady ) {
				onReady();
			}
		};

		if ( seekTo > 0 ) {
			const applySeek = () => {
				video.currentTime = seekTo;
				playVideo();
			};

			if ( video.readyState >= 1 ) {
				applySeek();
			} else {
				video.addEventListener( 'loadedmetadata', applySeek, {
					once: true,
				} );
			}
			return;
		}

		playVideo();
	};

	jQuery.ajax( {
		type: 'POST',
		dataType: 'json',
		url: siteConfig.ajaxUrl,
		data:
			'&videoID=' +
			videoID +
			'&action=brightcove_video_get_time' +
			'&nonce=' +
			siteConfig.ajax_nonce,
		success( res ) {
			const seekTo = parseFloat( res );
			startPlayback( ! isNaN( seekTo ) && seekTo > 0 ? seekTo : 0 );
		},
		error() {
			startPlayback( 0 );
		},
	} );
}

function closeVideoMiniPopup( button ) {
	const wrapper = button.closest( '.video-popup-wrapper' );

	if ( wrapper ) {
		wrapper.classList.remove( 'video-mini-popup-show' );

		const hoverVideo = wrapper.querySelector( '.video-hover-popup video' );

		if ( hoverVideo ) {
			hoverVideo.pause();
		}
	}

	jQuery( 'html' ).removeClass( 'html-video-mini-popup-show' );
	jQuery( '.video-popup-wrapper-with-slider' ).removeClass(
		'video-popup-open'
	);
}

function openNativeVideoWatchPopup( videoUrl, videoType, videoId ) {
	jQuery( 'html' ).addClass( 'video-watch-popup-show' );
	jQuery( '.video-watch-popup' ).addClass( 'open' );
	jQuery( '.video-watch-popup' )
		.empty()
		.html( buildNativeVideoWatchPopupHtml( videoUrl, videoType, videoId ) );

	setTimeout( () => {
		jQuery( '.video-watch-popup .watch-video-loader' ).remove();

		const video = jQuery( '.video-watch-popup video' ).get( 0 );

		if ( video ) {
			setupNativeVideoProgressTracking( video );

			video.addEventListener(
				'loadedmetadata',
				() => {
					const videoID = video.getAttribute( 'data-video-id' );

					if ( videoID && video.duration ) {
						setVideoDurationTime( videoID, video.duration );
					}
				},
				{ once: true }
			);

			resumeNativeVideoFromSavedTime( video, () => {
				const playPromise = video.play();

				if ( playPromise !== undefined ) {
					playPromise.catch( () => {} );
				}
			} );
		}
	}, 500 );
}

function setWatchedTime( videoID, currentVideoTime, totalDuration ) {
	const dataNonce = siteConfig.ajax_nonce; // eslint-disable-line
	const str =
		'&videoID=' +
		videoID +
		'&currentVideoTime=' +
		currentVideoTime +
		'&totalDuration=' +
		totalDuration +
		'&action=brightcove_video_set_time' +
		'&nonce=' +
		dataNonce;
	jQuery.ajax( {
		type: 'POST',
		dataType: 'json',
		url: siteConfig.ajaxUrl,
		data: str,
		success( response ) {},
	} );
	return false;
}
function setVideoDurationTime( videoID, totalDuration ) {
    const dataNonce = siteConfig.ajax_nonce; // eslint-disable-line
    const ajaxUrl = siteConfig.ajaxUrl; // eslint-disable-line
	if ( totalDuration ) {
		jQuery.ajax( {
			type: 'POST',
			url: ajaxUrl,
			data: {
				action: 'dp_set_video_duration_time',
				nonce: dataNonce,
				duration: totalDuration,
				videoId: videoID,
			},
			success( response ) {},
		} );
		return false;
	}
}
