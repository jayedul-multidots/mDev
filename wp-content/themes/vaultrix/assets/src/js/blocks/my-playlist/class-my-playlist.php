<?php
/**
 * Registers the vaultrix/my-playlist block.
 *
 * @global array    $attrs   Block attributes passed to the render callback.
 * @global string   $content Block content from InnerBlocks passed to the render callback.
 * @global WP_Block $block   Block registration object.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Blocks;

use VAULTRIX\Inc\Block_Base;
use WP_Block;

/**
 *  Class for the vaultrix/my-playlist block.
 */
class My_Playlist extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'my-playlist';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		/**
		 * Ajax action for videos listing category filter data.
		 */
		add_action( 'wp_ajax_dp_remove_recently_added_video', array( $this, 'dp_remove_recently_added_video' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_recently_added_video', array( $this, 'dp_remove_recently_added_video' ) );

		add_action( 'wp_ajax_dp_selected_playlist_listing_filter', array( $this, 'dp_selected_playlist_listing_filter' ) );
		add_action( 'wp_ajax_nopriv_dp_selected_playlist_listing_filter', array( $this, 'dp_selected_playlist_listing_filter' ) );

		add_action( 'wp_ajax_dp_selected_series_listing_filter', array( $this, 'dp_selected_series_listing_filter' ) );
		add_action( 'wp_ajax_nopriv_dp_selected_series_listing_filter', array( $this, 'dp_selected_series_listing_filter' ) );

		add_action( 'wp_ajax_dp_remove_playlist_listed_series', array( $this, 'dp_remove_playlist_listed_series' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_playlist_listed_series', array( $this, 'dp_remove_playlist_listed_series' ) );

		add_action( 'wp_ajax_dp_remove_playlist_listed_video', array( $this, 'dp_remove_playlist_listed_video' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_playlist_listed_video', array( $this, 'dp_remove_playlist_listed_video' ) );
	}

	/**
	 * Resolve the trailer video source URL and mime type from an ACF file field.
	 *
	 * @param array $trailer_video ACF file field array.
	 * @return array{url: string, type: string, attachment_id: int}
	 */
	private function resolve_trailer_video_source( array $trailer_video ): array {
		$video_url     = $trailer_video['url'] ?? '';
		$video_type    = $trailer_video['mime_type'] ?? '';
		$attachment_id = $trailer_video['ID'] ?? ( $trailer_video['id'] ?? 0 );
		$attachment_id = (int) $attachment_id;

		if ( $attachment_id > 0 ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$video_url = $attachment_url;
			}

			if ( empty( $video_type ) ) {
				$video_type = get_post_mime_type( $attachment_id ) ?: '';
			}
		}

		return array(
			'url'           => $video_url,
			'type'          => $video_type,
			'attachment_id' => $attachment_id,
		);
	}

	/**
	 * Resolve the main video source URL and mime type from an ACF file field.
	 *
	 * @param array $main_video ACF file field array.
	 * @return array{url: string, type: string, attachment_id: int}
	 */
	private function resolve_main_video_source( array $main_video ): array {
		$video_url     = $main_video['url'] ?? '';
		$video_type    = $main_video['mime_type'] ?? '';
		$attachment_id = $main_video['ID'] ?? ( $main_video['id'] ?? 0 );
		$attachment_id = (int) $attachment_id;

		if ( $attachment_id > 0 ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$video_url = $attachment_url;
			}

			if ( empty( $video_type ) ) {
				$video_type = get_post_mime_type( $attachment_id ) ?: '';
			}
		}

		return array(
			'url'           => $video_url,
			'type'          => $video_type,
			'attachment_id' => $attachment_id,
		);
	}

	/**
	 * Get watch progress data for a WordPress video attachment.
	 *
	 * @param int $attachment_id Video attachment ID.
	 * @param int $user_id       Current user ID.
	 * @return array{total_duration: float|string, watch_duration: float|string}
	 */
	private function get_video_watch_progress( int $attachment_id, int $user_id ): array {
		$video_total_duration = '';
		$video_watch_duration = '';

		if ( $attachment_id <= 0 || $user_id <= 0 ) {
			return array(
				'total_duration' => $video_total_duration,
				'watch_duration' => $video_watch_duration,
			);
		}

		$video_key = (string) $attachment_id;

		$bc_video_duration_meta = get_user_meta( $user_id, 'bc_video_duration_time', true );

		if ( ! empty( $bc_video_duration_meta ) && is_array( $bc_video_duration_meta ) && array_key_exists( $video_key, $bc_video_duration_meta ) ) {
			$video_total_duration = $bc_video_duration_meta[ $video_key ];
		}

		if ( '' === $video_total_duration ) {
			$video_metadata = wp_get_attachment_metadata( $attachment_id );

			if ( ! empty( $video_metadata['length'] ) ) {
				$video_total_duration = $video_metadata['length'];
			}
		}

		$bc_video_watch_duration = get_user_meta( $user_id, 'bc_watch_time', true );

		if ( ! empty( $bc_video_watch_duration ) && is_array( $bc_video_watch_duration ) && array_key_exists( $video_key, $bc_video_watch_duration ) ) {
			$video_watch_duration = $bc_video_watch_duration[ $video_key ];
		}

		return array(
			'total_duration' => $video_total_duration,
			'watch_duration' => $video_watch_duration,
		);
	}

	/**
	 * Render block.
	 *
	 * @param array    $attributes   Block attributes.
	 * @param string   $content      Block content.
	 * @param WP_Block $block        Block object.
	 * @return string
	 */
	public function render_callback(
		// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		array $attributes,
		string $content,
		WP_Block $block
		// phpcs:enable
	): string {

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'my-playlist',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$bg_style      = ! empty( $attributes['bgColor'] ) ? 'background: ' . $attributes['bgColor'] : '';
		$heading       = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$view_all_text = isset( $attributes['viewAllText'] ) ? $attributes['viewAllText'] : '';
		$view_all_link = isset( $attributes['viewAllLink'] ) ? $attributes['viewAllLink'] : '';
		$heading_style = ! empty( $attributes['headingColor'] ) ? 'style=color:' . $attributes['headingColor'] . '' : '';
		$user_id       = get_current_user_id();
		$playlists     = get_user_meta( $user_id, 'user_playlist', true );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="container">
		<?php
		if ( ! empty( $playlists ) ) {
			// Displaying recently added videos of the playlist
			$this->dp_display_recently_added_video( $heading, $view_all_text, $view_all_link, $heading_style, $user_id );

			// Displaying Playlist Listing
			$this->dp_playlist_listing( $user_id, $playlists );
		} else {
			?>
					<div class="playlist-listing-notice">
						<p>Your Playlist is empty !! Please Create Your Playlist.</p>
					</div>
			<?php
		}
		?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Function to display recently added videos.
	 *
	 * @param string $heading Heading of the recently added videos.
	 * @param string $view_all_text Text of the view all link.
	 * @param string $view_all_link Link of the view all link.
	 * @param string $heading_style Style of the heading.
	 * @param int $user_id ID of the user.
	 * @return void
	 */
	public function dp_display_recently_added_video( $heading, $view_all_text, $view_all_link, $heading_style, $user_id ) {
		$playlist_videos       = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
		$recently_added_videos = array();
		if ( $playlist_videos ) {
			$total_videos = count( $playlist_videos );
			for ( $i = $total_videos - 1; $i >= 0; $i-- ) {
				$recently_added_videos[] = $playlist_videos[ $i ];
			}
			?>
				<div class="recently-added">
					<div class="recently-added__content">
						<div class="recently-added__top">
							<h2 class="recently-added__title" <?php echo esc_html( $heading_style ); ?>><?php echo esc_html( $heading ); ?></h2>
							<?php if ( count( $recently_added_videos ) > 4 ) { ?>
								<a href="<?php echo esc_url( $view_all_link ); ?>" class="recently-added__link">
									<p><?php echo esc_html( $view_all_text ); ?></p>
								</a>
							<?php } ?>
						</div>
						<div class="recently-added__videos">
							<?php
							foreach ( $recently_added_videos as $index => $video_id ) {
								if ( $index < 4 ) {
									$video_post_id           = $video_id;
									$current_user_id         = get_current_user_id();
									$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
									$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
									$trailer_video           = get_field( 'trailer_video', $video_post_id );
									$trailer_video_url       = '';
									$trailer_video_type      = '';
									$trailer_attachment_id   = 0;

									if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
										$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
										$trailer_video_url     = $trailer_source['url'];
										$trailer_video_type    = $trailer_source['type'];
										$trailer_attachment_id = $trailer_source['attachment_id'];
									}

									$main_video        = get_field( 'main_video', $video_post_id );
									$main_video_source = array(
										'url'           => '',
										'type'          => '',
										'attachment_id' => 0,
									);

									if ( ! empty( $main_video ) && is_array( $main_video ) ) {
										$main_video_source = $this->resolve_main_video_source( $main_video );
									}
									?>
									<div class="recently-added__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
										<?php
										if ( has_post_thumbnail( $video_id ) ) {
											echo get_the_post_thumbnail( $video_id, 'full' );
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
										?>

										<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
											<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
											<div class="video-hover-media-section">
											<?php
											if ( ! empty( $trailer_video_url ) ) {
												?>
												<video class="vjs-fluid" playsinline preload="metadata">
													<source
														src="<?php echo esc_url( $trailer_video_url ); ?>"
														<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
													>
												</video>
												<div class="media-settings">
													<div class="mute-button"></div>
												</div>
												<?php
											} else {
												if ( has_post_thumbnail( $video_post_id ) ) {
													echo get_the_post_thumbnail( $video_post_id, 'full' );
												} else {
													$placeholder_image = get_field( 'placeholder_image', 'option' );
													if ( ! empty( $placeholder_image ) ) {
														echo wp_get_attachment_image( $placeholder_image, 'full' );
													}
												}
											}
											?>
											</div>
											<div class="video-hover-content-section">
												<div class="content-top">
													<div class="video-title">
														<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
													</div>
													<div class="video-buttons">
														<div
															class="play-from-beginning"
															data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
															<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
																data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
																<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
																	data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
																<?php endif; ?>
																<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
																	data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
																<?php endif; ?>
															<?php endif; ?>
														></div>
														<?php
														if ( is_user_logged_in() ) {
															?>
																<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
																	<div class="tooltip-text" style="display:none"></div>
																	<?php
																	if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
																		?>
																			<div class="added-to-playlist pl-icon"></div>
																		<?php
																	} else {
																		?>
																			<div class="not-added-to-playlist pl-icon"></div>
																		<?php
																	}
																	?>
																</div>
															<?php
														}

														if ( empty( $user_trending_video_arr ) ) {
															$user_trending_video_arr = array();
														}
														if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
															?>
																<div class="trending-now-btn">
																	<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																	<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																</div>
															<?php
														}
														?>
													</div>
												</div>
												<div class="content-bottom">
													<div class="video-description">
														<?php
															$post_content       = get_post_field( 'post_content', $video_post_id );
															$plain_text_content = wp_strip_all_tags( $post_content );
														?>
														<p><?php echo esc_html( $plain_text_content ); ?></p>
													</div>
													<div class="video-duration">
														<?php
														if ( $trailer_attachment_id > 0 ) {
															$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
															if ( ! empty( $trailer_metadata['length'] ) ) {
																?>
																<div class="duration">
																	<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																</div>
																<?php
															}
														}
														?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			<?php
		} else {
			return false;
		}
	}

	/**
	 * Ajax Callback function to remove video.
	 *
	 * @return void
	 */
	public function dp_remove_recently_added_video() {
		$post_id             = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
		$playlist_slug       = filter_input( INPUT_GET, 'playlist_slug', FILTER_SANITIZE_STRING );
		$playlist_videos_ids = array();
		$playlist_series_ids = array();
		$user_id             = get_current_user_id();
		$user_playlists      = get_user_meta( $user_id, 'user_playlist', true );
		$recently_added      = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
		$updated_playlist    = array();
		$updated_playlist    = array();
		$response_array      = array();

		if ( ! empty( $post_id ) && ! empty( $user_playlists ) ) {
			// updating playlist
			$updated_playlist = $this->dp_remove_post_id( $user_playlists, $post_id );
			update_user_meta( $user_id, 'user_playlist', $updated_playlist );

			// updating recent added video list.
			$index = array_search( $post_id, $recently_added, true );
			if ( $index !== false ) {
				unset( $recently_added[ $index ] );
				$recently_added = array_values( $recently_added );
			}
			update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
		}

		ob_start();
		if ( ! empty( $updated_playlist ) ) {
			$current_playlist = $updated_playlist[ $playlist_slug ];
			foreach ( $current_playlist['video'] as $video_id ) {
				$playlist_videos_ids[] = $video_id;
			}
			foreach ( $current_playlist['series'] as $series_id ) {
				$playlist_series_ids[] = $series_id;
			}
			if ( ! empty( $playlist_videos_ids ) || ! empty( $playlist_series_ids ) ) {
				if ( ! empty( $playlist_videos_ids ) ) {
					$playlist_videos = $updated_playlist[ $playlist_slug ]['video'];
					?>
					<div class="playlist-listing__video-top">
						<h2 class="playlist-listing__section-title"><?php echo esc_html( $current_playlist['label'] ); ?></h2>
						<?php if ( count( $playlist_videos ) > 6 ) { ?>
							<a href="<?php echo esc_url( home_url( '/playlist-listing/?playlist=' . $current_playlist['slug'] ) ); ?>" class="playlist-listing__video-link">View all</a>
						<?php } ?>
					</div>
					<div class="playlist-listing__video-wrapper video-popup-wrapper-with-slider">
						<?php
						foreach ( $updated_playlist[ $playlist_slug ]['video'] as $video_id ) {
							$video_post_id           = $video_id;
							$current_user_id         = get_current_user_id();
							$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
							$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

							$main_video        = get_field( 'main_video', $video_post_id );
							$main_video_source = array(
								'url'           => '',
								'type'          => '',
								'attachment_id' => 0,
							);

							if ( ! empty( $main_video ) && is_array( $main_video ) ) {
								$main_video_source = $this->resolve_main_video_source( $main_video );
							}

							$watch_progress       = $this->get_video_watch_progress(
								$main_video_source['attachment_id'],
								$user_id
							);
							$video_total_duration = $watch_progress['total_duration'];
							$video_watch_duration = $watch_progress['watch_duration'];
							?>
							<div class="playlist-listing__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
								<div class="playlist-listing__video-thumbnail">
									<?php
									if ( has_post_thumbnail( $video_id ) ) {
										echo get_the_post_thumbnail( $video_id, 'full' );
									} else {
										$placeholder_image = get_field( 'placeholder_image', 'option' );
										if ( ! empty( $placeholder_image ) ) {
											echo wp_get_attachment_image( $placeholder_image, 'full' );
										}
									}
									?>
								</div>
								<?php
								if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
									$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
									?>
										<div class="video-progress__bar">
											<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
										</div>
									<?php
								}
								?>
								<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
									<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
									<div class="video-hover-media-section">
									<?php
									$trailer_video                 = get_field( 'trailer_video', $video_post_id );
											$trailer_video_url     = '';
											$trailer_video_type    = '';
											$trailer_attachment_id = 0;

									if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
										$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
										$trailer_video_url     = $trailer_source['url'];
										$trailer_video_type    = $trailer_source['type'];
										$trailer_attachment_id = $trailer_source['attachment_id'];
									}

									if ( ! empty( $trailer_video_url ) ) {
										?>
										<video class="vjs-fluid" playsinline preload="metadata">
											<source src="<?php echo esc_url( $trailer_video_url ); ?>" <?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>>
										</video>
										<div class="media-settings">
											<div class="mute-button"></div>
										</div>
										<?php
									} else {
										if ( has_post_thumbnail( $video_post_id ) ) {
											echo get_the_post_thumbnail( $video_post_id, 'full' );
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
									}
									?>
									</div>
									<div class="video-hover-content-section">
										<div class="content-top">
											<div class="video-title">
												<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
											</div>
											<div class="video-buttons">
												<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
													<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
														<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
														<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
												<?php
												if ( is_user_logged_in() ) {
													?>
														<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
															<div class="tooltip-text" style="display:none"></div>
															<?php
															if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
																?>
																	<div class="added-to-playlist pl-icon"></div>
																<?php
															} else {
																?>
																	<div class="not-added-to-playlist pl-icon"></div>
																<?php
															}
															?>
														</div>
													<?php
												}

												if ( empty( $user_trending_video_arr ) ) {
													$user_trending_video_arr = array();
												}
												if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
													?>
														<div class="trending-now-btn">
															<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
															<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
														</div>
													<?php
												}
												?>
											</div>
										</div>
										<div class="content-bottom">
											<div class="video-description">
												<?php
													$post_content       = get_post_field( 'post_content', $video_post_id );
													$plain_text_content = wp_strip_all_tags( $post_content );
												?>
												<p><?php echo esc_html( $plain_text_content ); ?></p>
											</div>
											<div class="video-duration">
												<?php
												if ( $trailer_attachment_id > 0 ) {
													$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
													if ( ! empty( $trailer_metadata['length'] ) ) {
														?>
														<div class="duration">
															<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
														</div>
														<?php
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>		
							<?php
						}
						?>
					</div>
						<?php
				}
			} else {
				$playlist_name = $current_playlist['label'];
				?>
					<div class="playlist-listing-notice">
						<p><?php echo esc_html( $playlist_name ); ?> doesn't have Any Video or Series.</p>
					</div>
				<?php
			}
		}
		$playlist_video_html = ob_get_clean();

		$updated_array = array();
		$total_videos  = count( $recently_added );

		// Iterate over the array in reverse order.
		for ( $i = $total_videos - 1; $i >= 0; $i-- ) {
			$updated_array[] = $recently_added[ $i ];
		}
		ob_start();
		foreach ( $updated_array as $index => $video_id ) {
			if ( $index < 4 ) {
				$video_post_id           = $video_id;
				$current_user_id         = get_current_user_id();
				$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
				$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
				$trailer_video           = get_field( 'trailer_video', $video_post_id );
				$trailer_video_url       = '';
				$trailer_video_type      = '';
				$trailer_attachment_id   = 0;

				if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
					$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
					$trailer_video_url     = $trailer_source['url'];
					$trailer_video_type    = $trailer_source['type'];
					$trailer_attachment_id = $trailer_source['attachment_id'];
				}

				$main_video        = get_field( 'main_video', $video_post_id );
				$main_video_source = array(
					'url'           => '',
					'type'          => '',
					'attachment_id' => 0,
				);

				if ( ! empty( $main_video ) && is_array( $main_video ) ) {
					$main_video_source = $this->resolve_main_video_source( $main_video );
				}
				?>
					<div class="recently-added__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
						<?php
						if ( has_post_thumbnail( $video_id ) ) {
							echo get_the_post_thumbnail( $video_id, 'full' );
						} else {
							$placeholder_image = get_field( 'placeholder_image', 'option' );
							if ( ! empty( $placeholder_image ) ) {
								echo wp_get_attachment_image( $placeholder_image, 'full' );
							}
						}
						?>
						<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
							<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
							<div class="video-hover-media-section">
							<?php
							if ( ! empty( $trailer_video_url ) ) {
								?>
								<video class="vjs-fluid" playsinline preload="metadata">
									<source
										src="<?php echo esc_url( $trailer_video_url ); ?>"
										<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
									>
								</video>
								<div class="media-settings">
									<div class="mute-button"></div>
								</div>
								<?php
							} else {
								if ( has_post_thumbnail( $video_post_id ) ) {
									echo get_the_post_thumbnail( $video_post_id, 'full' );
								} else {
									$placeholder_image = get_field( 'placeholder_image', 'option' );
									if ( ! empty( $placeholder_image ) ) {
										echo wp_get_attachment_image( $placeholder_image, 'full' );
									}
								}
							}
							?>
							</div>
							<div class="video-hover-content-section">
								<div class="content-top">
									<div class="video-title">
										<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
									</div>
									<div class="video-buttons">
										<div
											class="play-from-beginning"
											data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
											<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
												data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
												<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
													data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
												<?php endif; ?>
												<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
													data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
												<?php endif; ?>
											<?php endif; ?>
										></div>
										<?php
										if ( is_user_logged_in() ) {
											?>
												<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
													<div class="tooltip-text" style="display:none"></div>
													<?php
													if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
														?>
															<div class="added-to-playlist pl-icon"></div>
														<?php
													} else {
														?>
															<div class="not-added-to-playlist pl-icon"></div>
														<?php
													}
													?>
												</div>
											<?php
										}

										if ( empty( $user_trending_video_arr ) ) {
											$user_trending_video_arr = array();
										}
										if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
											?>
												<div class="trending-now-btn">
													<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
													<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
												</div>
											<?php
										}
										?>
									</div>
								</div>
								<div class="content-bottom">
									<div class="video-description">
										<?php
											$post_content       = get_post_field( 'post_content', $video_post_id );
											$plain_text_content = wp_strip_all_tags( $post_content );
										?>
										<p><?php echo esc_html( $plain_text_content ); ?></p>
									</div>
									<div class="video-duration">
										<?php
										if ( $trailer_attachment_id > 0 ) {
											$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
											if ( ! empty( $trailer_metadata['length'] ) ) {
												?>
												<div class="duration">
													<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
												</div>
												<?php
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		}
		$recently_added_video_html = ob_get_clean();

		$response_array['playlist_video_html']       = $playlist_video_html;
		$response_array['recently_added_video_html'] = $recently_added_video_html;
		echo json_encode($response_array); //phpcs:ignore
		wp_die();
	}

	/**
	 * Function to Display Playlist on My playlist page.
	 *
	 * @param int $user_id ID of the user.
	 * @param array $playlists Array of playlists.
	 * @return void
	 */
	public function dp_playlist_listing( $user_id, $playlists ) {
		?>
			<div class="playlist-listing">
				<div class="playlist-listing__playlist-tab-wrapper">
					<?php foreach ( $playlists as $playlist ) { ?>
						<div class="playlist-listing__playlist-tab">
							<p class="btn-main btn-transparent" data-playlist-slug="<?php echo esc_attr( $playlist['slug'] ); ?>"><?php echo esc_html( $playlist['label'] ); ?></p>
						</div>
					<?php } ?>
				</div>
				<div class="playlist-listing__content-wrapper">
						<div class="playlist-listing__tab-content">
							<div class="playlist-listing__tab-content-wrapper">
								<?php
								$playlist_videos_ids = array();
								$playlist_series_ids = array();
								$playlist_array      = array_values( $playlists );
								$first_playlist      = $playlist_array[0];
								foreach ( $first_playlist['video'] as $video_id ) {
									$playlist_videos_ids[] = $video_id;
								}
								foreach ( $first_playlist['series'] as $series_id ) {
									$playlist_series_ids[] = $series_id;
								}
								if ( ! empty( $playlist_videos_ids ) || ! empty( $playlist_series_ids ) ) {
									?>
										<?php
										if ( ! empty( $first_playlist['series'] ) ) {
											$recent_added_formatted_series_ids = array();
											$series_ids                        = $first_playlist['series'];
											$total_series                      = count( $series_ids );
											for ( $i = $total_series - 1; $i >= 0; $i-- ) {
												$recent_added_formatted_series_ids[] = $series_ids[ $i ];
											}
											?>
											<div class="playlist-listing__series-section">
												<div class="playlist-listing__series-section-wrapper">
													<h2 class="playlist-listing__section-title">Series</h2>
													<div class="playlist-listing__series-wrapper">
														<?php
														if ( $recent_added_formatted_series_ids ) {
															foreach ( $recent_added_formatted_series_ids as $series_id ) {
																$series_thumbnail = get_field( 'add_feature_image', 'video-show-category_' . $series_id );
																$series_term      = get_term( $series_id );
																$series_title     = ( $series_term && ! is_wp_error( $series_term ) ) ? $series_term->name : '';
																?>
																	<div class="playlist-listing__series" data-series-id="<?php echo esc_attr( $series_id ); ?>">
																		<div class="playlist-listing__remove-series" data-series-id="<?php echo esc_attr( $series_id ); ?>"></div>
																		<div class="playlist-listing__series-thumbnail">
																			<img src="<?php echo esc_url( $series_thumbnail ); ?>" width="266" height="372" alt="series poster"></img>
																		</div>
																		<div class="playlist-listing__series-content">
																			<h3 class="playlist-listing__series-title"><?php echo esc_html( $series_title ); ?></h3>
																		</div>
																	</div>	
																<?php
															}
														}
														?>
													</div>

													<div class="playlist-listing__series-episodes">
														<div class="playlist-listing__series-episodes-top">
															<h2 class="playlist-listing__section-title">Episodes</h2>
															<?php
															$first_series_id = $recent_added_formatted_series_ids[0];
															$series_term     = get_term( $first_series_id );
															if ( $series_term && ! is_wp_error( $series_term ) ) {
																echo "<a href='" . esc_url( get_term_link( $series_term->term_id ) ) . "' class='playlist-listing__series-link'>View all</a>";
															}
															?>
														</div>
														<div class="playlist-listing__series-episodes-content">
															<?php
															$first_series_id = $recent_added_formatted_series_ids[0];
															$episodes_array  = array();
															$args            = array(
																'post_type'      => 'deep_pockets_videos',  // Change to your post type if necessary
																'posts_per_page' => 10, // Get all posts
																'order' => 'ASC', // Order in ascending order.
																'orderby' => 'post_date', // Order by term ID
																'tax_query'      => array( //phpcs:ignore
																	array(
																		'taxonomy' => 'video-show-category',
																		'field'    => 'id',
																		'terms'    => $first_series_id,
																	),
																),
															);
															$videos_query    = new \WP_Query( $args );

															// Display Videos for the Current Season
															while ( $videos_query->have_posts() ) {
																$videos_query->the_post();
																$episodes_array[] = get_the_ID();
															}

															// Reset Post Data for the next loop
															wp_reset_postdata();

															if ( ! empty( $episodes_array ) ) {
																echo '<div class="playlist-listing__series-episodes-wrapper video-popup-wrapper-with-slider">';
																foreach ( $episodes_array as $episode ) {
																	$video_post_id           = $episode;
																	$current_user_id         = get_current_user_id();
																	$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
																	$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

																	$main_video        = get_field( 'main_video', $video_post_id );
																	$main_video_source = array(
																		'url'           => '',
																		'type'          => '',
																		'attachment_id' => 0,
																	);

																	if ( ! empty( $main_video ) && is_array( $main_video ) ) {
																		$main_video_source = $this->resolve_main_video_source( $main_video );
																	}

																	$watch_progress       = $this->get_video_watch_progress(
																		$main_video_source['attachment_id'],
																		$user_id
																	);
																	$video_total_duration = $watch_progress['total_duration'];
																	$video_watch_duration = $watch_progress['watch_duration'];
																	?>
																		<div class="playlist-listing__series-episode video-popup-wrapper" data-post-id="<?php echo esc_attr( $episode ); ?>">
																			<div class="playlist-listing__episode-thumbnail">
																			<?php
																			if ( has_post_thumbnail( $episode ) ) {
																				echo get_the_post_thumbnail( $episode, 'full' );
																			} else {
																				$placeholder_image = get_field( 'placeholder_image', 'option' );
																				if ( ! empty( $placeholder_image ) ) {
																					echo wp_get_attachment_image( $placeholder_image, 'full' );
																				}
																			}
																			?>
																			</div>
																			<?php
																			if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
																				$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
																				?>
																					<div class="video-progress__bar">
																						<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
																					</div>
																				<?php
																			}
																			?>
																			<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
																				<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
																				<div class="video-hover-media-section">
																				<?php
																				$trailer_video         = get_field( 'trailer_video', $video_post_id );
																				$trailer_video_url     = '';
																				$trailer_video_type    = '';
																				$trailer_attachment_id = 0;

																				if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
																					$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
																					$trailer_video_url     = $trailer_source['url'];
																					$trailer_video_type    = $trailer_source['type'];
																					$trailer_attachment_id = $trailer_source['attachment_id'];
																				}

																				if ( ! empty( $trailer_video_url ) ) {
																					?>
																					<video class="vjs-fluid" playsinline preload="metadata">
																						<source src="<?php echo esc_url( $trailer_video_url ); ?>" <?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
																						>
																					</video>
																					<div class="media-settings">
																						<div class="mute-button"></div>
																					</div>
																					<?php
																				} else {
																					if ( has_post_thumbnail( $video_post_id ) ) {
																						echo get_the_post_thumbnail( $video_post_id, 'full' );
																					} else {
																						$placeholder_image = get_field( 'placeholder_image', 'option' );
																						if ( ! empty( $placeholder_image ) ) {
																							echo wp_get_attachment_image( $placeholder_image, 'full' );
																						}
																					}
																				}
																				?>
																				</div>
																				<div class="video-hover-content-section">
																					<div class="content-top">
																						<div class="video-title">
																							<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
																						</div>
																						<div class="video-buttons">
																							<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
																	<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
																		<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
																		<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
																							<?php
																							if ( empty( $user_trending_video_arr ) ) {
																								$user_trending_video_arr = array();
																							}
																							if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
																								?>
																									<div class="trending-now-btn">
																										<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																										<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																									</div>
																								<?php
																							}
																							?>
																						</div>
																					</div>
																					<div class="content-bottom">
																						<div class="video-description">
																							<?php
																								$post_content       = get_post_field( 'post_content', $video_post_id );
																								$plain_text_content = wp_strip_all_tags( $post_content );
																							?>
																							<p><?php echo esc_html( $plain_text_content ); ?></p>
																						</div>
																						<div class="video-duration">
																							<?php
																							if ( $trailer_attachment_id > 0 ) {
																								$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
																								if ( ! empty( $trailer_metadata['length'] ) ) {
																									?>
																									<div class="duration">
																										<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																									</div>
																									<?php
																								}
																							}
																							?>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<?php
																}
																echo '</div>';
															} else {
																?>
																	<div class="playlist-listing-notice-fs">
																		<p>This season does not have any episodes !!.</p>
																	</div>
																<?php
															}
															?>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>

										<?php if ( ! empty( $first_playlist['video'] ) ) { ?>
											<div class="playlist-listing__video-section">
												<div class="playlist-listing__video-top">
													<h2 class="playlist-listing__section-title"><?php echo esc_html( $first_playlist['label'] ); ?></h2>
													<?php if ( count( $first_playlist['video'] ) > 6 ) { ?>
														<a href="<?php echo esc_url( home_url( '/playlist-listing/?playlist=' . $first_playlist['slug'] ) ); ?>" class="playlist-listing__video-link">View all</a>
													<?php } ?>
												</div>
												
												<div class="playlist-listing__video-wrapper video-popup-wrapper-with-slider">
													<?php
													foreach ( $first_playlist['video'] as $video_id ) {
														$video_post_id           = $video_id;
														$current_user_id         = get_current_user_id();
														$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
														$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

														$main_video        = get_field( 'main_video', $video_post_id );
														$main_video_source = array(
															'url'           => '',
															'type'          => '',
															'attachment_id' => 0,
														);

														if ( ! empty( $main_video ) && is_array( $main_video ) ) {
															$main_video_source = $this->resolve_main_video_source( $main_video );
														}

														$watch_progress       = $this->get_video_watch_progress(
															$main_video_source['attachment_id'],
															$user_id
														);
														$video_total_duration = $watch_progress['total_duration'];
														$video_watch_duration = $watch_progress['watch_duration'];
														?>
															<div class="playlist-listing__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
																<div class="playlist-listing__video-thumbnail">
																	<?php
																	if ( has_post_thumbnail( $video_id ) ) {
																		echo get_the_post_thumbnail( $video_id, 'full' );
																	} else {
																		$placeholder_image = get_field( 'placeholder_image', 'option' );
																		if ( ! empty( $placeholder_image ) ) {
																			echo wp_get_attachment_image( $placeholder_image, 'full' );
																		}
																	}
																	?>
																</div>
																<?php
																if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
																	$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
																	?>
																			<div class="video-progress__bar">
																				<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
																			</div>
																		<?php
																}
																?>
																<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
																	<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
																	<div class="video-hover-media-section">
																	<?php
																	$trailer_video         = get_field( 'trailer_video', $video_post_id );
																	$trailer_video_url     = '';
																	$trailer_video_type    = '';
																	$trailer_attachment_id = 0;

																	if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
																		$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
																		$trailer_video_url     = $trailer_source['url'];
																		$trailer_video_type    = $trailer_source['type'];
																		$trailer_attachment_id = $trailer_source['attachment_id'];
																	}

																	if ( ! empty( $trailer_video_url ) ) {
																		?>
												<video class="vjs-fluid" playsinline preload="metadata">
													<source
														src="<?php echo esc_url( $trailer_video_url ); ?>"
																		<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
													>
												</video>
												<div class="media-settings">
													<div class="mute-button"></div>
												</div>
																		<?php
																	} else {
																		if ( has_post_thumbnail( $video_post_id ) ) {
																			echo get_the_post_thumbnail( $video_post_id, 'full' );
																		} else {
																			$placeholder_image = get_field( 'placeholder_image', 'option' );
																			if ( ! empty( $placeholder_image ) ) {
																				echo wp_get_attachment_image( $placeholder_image, 'full' );
																			}
																		}
																	}
																	?>
																	</div>
																	<div class="video-hover-content-section">
																		<div class="content-top">
																			<div class="video-title">
																				<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
																			</div>
																			<div class="video-buttons">
																				<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
														<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
															<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
															<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
																				<?php
																				if ( is_user_logged_in() ) {
																					?>
																						<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
																							<div class="tooltip-text" style="display:none"></div>
																							<?php
																							if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
																								?>
																									<div class="added-to-playlist pl-icon"></div>
																								<?php
																							} else {
																								?>
																									<div class="not-added-to-playlist pl-icon"></div>
																								<?php
																							}
																							?>
																						</div>
																					<?php
																				}

																				if ( empty( $user_trending_video_arr ) ) {
																					$user_trending_video_arr = array();
																				}
																				if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
																					?>
																						<div class="trending-now-btn">
																							<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																							<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																						</div>
																					<?php
																				}
																				?>
																			</div>
																		</div>
																		<div class="content-bottom">
																			<div class="video-description">
																				<?php
																					$post_content       = get_post_field( 'post_content', $video_post_id );
																					$plain_text_content = wp_strip_all_tags( $post_content );
																				?>
																				<p><?php echo esc_html( $plain_text_content ); ?></p>
																			</div>
																			<div class="video-duration">
																				<?php
																				if ( $trailer_attachment_id > 0 ) {
																					$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
																					if ( ! empty( $trailer_metadata['length'] ) ) {
																						?>
																						<div class="duration">
																							<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																						</div>
																						<?php
																					}
																				}
																				?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>		
														<?php
													}
													?>
												</div>		
											</div>
										<?php } ?>
									<?php
								} else {
									$playlist_name = $first_playlist['label'];
									?>
										<div class="playlist-listing-notice">
											<p><?php echo esc_html( $playlist_name ); ?> doesn't have Any Video or Series.</p>
										</div>
									<?php
								}
								?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Callback function to display selected playlist
	 */
	public function dp_selected_playlist_listing_filter() {
		$selected_cat        = filter_input( INPUT_GET, 'selected_playlist', FILTER_SANITIZE_STRING );
		$playlist_videos_ids = array();
		$playlist_series_ids = array();
		$user_id             = get_current_user_id();
		$playlists           = get_user_meta( $user_id, 'user_playlist', true );
		$sel_playlist        = array();
		foreach ( $playlists as $index => $value ) {
			if ( $index === $selected_cat ) {
				$sel_playlist = $value;
			}
		}
		foreach ( $sel_playlist['video'] as $video_id ) {
			$playlist_videos_ids[] = $video_id;
		}
		foreach ( $sel_playlist['series'] as $series_id ) {
			$playlist_series_ids[] = $series_id;
		}
		ob_start();
		if ( ! empty( $playlist_videos_ids ) || ! empty( $playlist_series_ids ) ) {
			?>
				<div class="playlist-listing__tab-content-wrapper">
					<?php
					if ( ! empty( $sel_playlist['series'] ) ) {
						$recent_added_formatted_series_ids = array();
						$series_ids                        = $sel_playlist['series'];
						$total_series                      = count( $series_ids );
						for ( $i = $total_series - 1; $i >= 0; $i-- ) {
							$recent_added_formatted_series_ids[] = $series_ids[ $i ];
						}
						?>
						<div class="playlist-listing__series-section">
							<h2 class="playlist-listing__section-title">Series</h2>
	
							<div class="playlist-listing__series-wrapper">
								<?php
								foreach ( $recent_added_formatted_series_ids as $series_id ) {
									$series_thumbnail = get_field( 'add_feature_image', 'video-show-category_' . $series_id );
									$series_term      = get_term( $series_id );
									$series_title     = ( $series_term && ! is_wp_error( $series_term ) ) ? $series_term->name : '';
									?>
										<div class="playlist-listing__series" data-series-id="<?php echo esc_attr( $series_id ); ?>">
											<div class="playlist-listing__remove-series" data-series-id="<?php echo esc_attr( $series_id ); ?>"></div>
											<div class="playlist-listing__series-thumbnail">
												<img src="<?php echo esc_url( $series_thumbnail ); ?>" width="266" height="372" alt="series poster"></img>
											</div>
											<div class="playlist-listing__series-content">
												<h3 class="playlist-listing__series-title"><?php echo esc_html( $series_title ); ?></h3>
											</div>
										</div>	
									<?php
								}
								?>
							</div>		
							<div class="playlist-listing__series-episodes">
								<div class="playlist-listing__series-episodes-top">
									<h2 class="playlist-listing__section-title">Episodes</h2>
									<?php
										$first_series_id = $recent_added_formatted_series_ids[0];
										$series_term     = get_term( $first_series_id );
									if ( $series_term && ! is_wp_error( $series_term ) ) {
										echo "<a href='" . esc_url( get_term_link( $series_term->term_id ) ) . "' class='playlist-listing__series-link'>View all</a>";
									}
									?>
								</div>
								<div class="playlist-listing__series-episodes-content">
									<?php
										$first_series_id = $recent_added_formatted_series_ids[0];
										$episodes_array  = array();
										$args            = array(
											'post_type' => 'deep_pockets_videos',  // Change to your post type if necessary
											'posts_per_page' => 10, // Get all posts
											'order'     => 'ASC', // Order in ascending order.
											'orderby'   => 'post_date', // Order by term ID
											'tax_query'      => array( //phpcs:ignore
												array(
													'taxonomy' => 'video-show-category',
													'field'    => 'id',
													'terms'    => $first_series_id,
												),
											),
										);
										$videos_query    = new \WP_Query( $args );

										// Display Videos for the Current Season
										while ( $videos_query->have_posts() ) {
											$videos_query->the_post();
											$episodes_array[] = get_the_ID();
										}

										// Reset Post Data for the next loop
										wp_reset_postdata();

										if ( ! empty( $episodes_array ) ) {
											echo '<div class="playlist-listing__series-episodes-wrapper video-popup-wrapper-with-slider">';
											foreach ( $episodes_array as $episode ) {
												$video_post_id           = $episode;
												$current_user_id         = get_current_user_id();
												$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
												$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

												$main_video        = get_field( 'main_video', $video_post_id );
												$main_video_source = array(
													'url'  => '',
													'type' => '',
													'attachment_id' => 0,
												);

												if ( ! empty( $main_video ) && is_array( $main_video ) ) {
													$main_video_source = $this->resolve_main_video_source( $main_video );
												}

												$watch_progress       = $this->get_video_watch_progress(
													$main_video_source['attachment_id'],
													$user_id
												);
												$video_total_duration = $watch_progress['total_duration'];
												$video_watch_duration = $watch_progress['watch_duration'];
												?>
												<div class="playlist-listing__series-episode video-popup-wrapper" data-post-id="<?php echo esc_attr( $episode ); ?>">
													<div class="playlist-listing__episode-thumbnail">
														<?php
														if ( has_post_thumbnail( $episode ) ) {
															echo get_the_post_thumbnail( $episode, 'full' );
														} else {
															$placeholder_image = get_field( 'placeholder_image', 'option' );
															if ( ! empty( $placeholder_image ) ) {
																echo wp_get_attachment_image( $placeholder_image, 'full' );
															}
														}
														?>
													</div>
													<?php
													if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
														$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
														?>
															<div class="video-progress__bar">
																<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
															</div>
														<?php
													}
													?>
													<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
														<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
														<div class="video-hover-media-section">
														<?php
														$trailer_video         = get_field( 'trailer_video', $video_post_id );
														$trailer_video_url     = '';
														$trailer_video_type    = '';
														$trailer_attachment_id = 0;

														if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
															$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
															$trailer_video_url     = $trailer_source['url'];
															$trailer_video_type    = $trailer_source['type'];
															$trailer_attachment_id = $trailer_source['attachment_id'];
														}

														if ( ! empty( $trailer_video_url ) ) {
															?>
												<video class="vjs-fluid" playsinline preload="metadata">
													<source
														src="<?php echo esc_url( $trailer_video_url ); ?>"
															<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
													>
												</video>
												<div class="media-settings">
													<div class="mute-button"></div>
												</div>
															<?php
														} else {
															if ( has_post_thumbnail( $video_post_id ) ) {
																echo get_the_post_thumbnail( $video_post_id, 'full' );
															} else {
																$placeholder_image = get_field( 'placeholder_image', 'option' );
																if ( ! empty( $placeholder_image ) ) {
																	echo wp_get_attachment_image( $placeholder_image, 'full' );
																}
															}
														}
														?>
														</div>
														<div class="video-hover-content-section">
															<div class="content-top">
																<div class="video-title">
																	<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
																</div>
																<div class="video-buttons">
																	<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
													<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
														<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
														<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
																	<?php
																	if ( empty( $user_trending_video_arr ) ) {
																		$user_trending_video_arr = array();
																	}
																	if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
																		?>
																			<div class="trending-now-btn">
																				<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																				<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																			</div>
																		<?php
																	}
																	?>
																</div>
															</div>
															<div class="content-bottom">
																<div class="video-description">
																	<?php
																		$post_content       = get_post_field( 'post_content', $video_post_id );
																		$plain_text_content = wp_strip_all_tags( $post_content );
																	?>
																	<p><?php echo esc_html( $plain_text_content ); ?></p>
																</div>
																<div class="video-duration">
																	<?php
																	if ( $trailer_attachment_id > 0 ) {
																		$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
																		if ( ! empty( $trailer_metadata['length'] ) ) {
																			?>
																			<div class="duration">
																				<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																			</div>
																			<?php
																		}
																	}
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
												<?php
											}
											echo '</div>';
										} else {
											?>
											<div class="playlist-listing-notice-fs">
												<p>This season does not have any episodes !!</p>
											</div>
											<?php
										}
										?>
								</div>
							</div>
						</div>
					<?php } ?>
					
					<?php if ( ! empty( $sel_playlist['video'] ) ) { ?>
						<div class="playlist-listing__video-section">
							<div class="playlist-listing__video-top">
								<h2 class="playlist-listing__section-title"><?php echo esc_html( $sel_playlist['label'] ); ?></h2>
								<?php if ( count( $sel_playlist['video'] ) > 6 ) { ?>
									<a href="<?php echo esc_url( home_url( '/playlist-listing/?playlist=' . $sel_playlist['slug'] ) ); ?>" class="playlist-listing__video-link">View all</a>
								<?php } ?>
							</div>
							<div class="playlist-listing__video-wrapper video-popup-wrapper-with-slider">
								<?php
								foreach ( $sel_playlist['video'] as $video_id ) {
									$video_post_id           = $video_id;
									$current_user_id         = get_current_user_id();
									$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
									$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

									$main_video        = get_field( 'main_video', $video_post_id );
									$main_video_source = array(
										'url'           => '',
										'type'          => '',
										'attachment_id' => 0,
									);

									if ( ! empty( $main_video ) && is_array( $main_video ) ) {
										$main_video_source = $this->resolve_main_video_source( $main_video );
									}

									$watch_progress       = $this->get_video_watch_progress(
										$main_video_source['attachment_id'],
										$user_id
									);
									$video_total_duration = $watch_progress['total_duration'];
									$video_watch_duration = $watch_progress['watch_duration'];
									?>
										<div class="playlist-listing__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
											<div class="playlist-listing__video-thumbnail">
												<?php
												if ( has_post_thumbnail( $video_id ) ) {
													echo get_the_post_thumbnail( $video_id, 'full' );
												} else {
													$placeholder_image = get_field( 'placeholder_image', 'option' );
													if ( ! empty( $placeholder_image ) ) {
														echo wp_get_attachment_image( $placeholder_image, 'full' );
													}
												}
												?>
											</div>
											<?php
											if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
												$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
												?>
														<div class="video-progress__bar">
															<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
														</div>
													<?php
											}
											?>
											<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
												<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
												<div class="video-hover-media-section">
												<?php
												$trailer_video         = get_field( 'trailer_video', $video_post_id );
												$trailer_video_url     = '';
												$trailer_video_type    = '';
												$trailer_attachment_id = 0;

												if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
													$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
													$trailer_video_url     = $trailer_source['url'];
													$trailer_video_type    = $trailer_source['type'];
													$trailer_attachment_id = $trailer_source['attachment_id'];
												}

												if ( ! empty( $trailer_video_url ) ) {
													?>
												<video class="vjs-fluid" playsinline preload="metadata">
													<source
														src="<?php echo esc_url( $trailer_video_url ); ?>"
														<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
													>
												</video>
												<div class="media-settings">
													<div class="mute-button"></div>
												</div>
													<?php
												} else {
													if ( has_post_thumbnail( $video_post_id ) ) {
														echo get_the_post_thumbnail( $video_post_id, 'full' );
													} else {
														$placeholder_image = get_field( 'placeholder_image', 'option' );
														if ( ! empty( $placeholder_image ) ) {
															echo wp_get_attachment_image( $placeholder_image, 'full' );
														}
													}
												}
												?>
												</div>
												<div class="video-hover-content-section">
													<div class="content-top">
														<div class="video-title">
															<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
														</div>
														<div class="video-buttons">
															<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
													<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
														<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
														<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
															<?php
															if ( is_user_logged_in() ) {
																?>
																	<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
																		<div class="tooltip-text" style="display:none"></div>
																		<?php
																		if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
																			?>
																				<div class="added-to-playlist pl-icon"></div>
																			<?php
																		} else {
																			?>
																				<div class="not-added-to-playlist pl-icon"></div>
																			<?php
																		}
																		?>
																	</div>
																<?php
															}

															if ( empty( $user_trending_video_arr ) ) {
																$user_trending_video_arr = array();
															}
															if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
																?>
																	<div class="trending-now-btn">
																		<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																		<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																	</div>
																<?php
															}
															?>
														</div>
													</div>
													<div class="content-bottom">
														<div class="video-description">
															<?php
																$post_content       = get_post_field( 'post_content', $video_post_id );
																$plain_text_content = wp_strip_all_tags( $post_content );
															?>
															<p><?php echo esc_html( $plain_text_content ); ?></p>
														</div>
														<div class="video-duration">
															<?php
															if ( $trailer_attachment_id > 0 ) {
																$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
																if ( ! empty( $trailer_metadata['length'] ) ) {
																	?>
																	<div class="duration">
																		<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																	</div>
																	<?php
																}
															}
															?>
														</div>
													</div>
												</div>
											</div>
										</div>		
									<?php
								}
								?>
							</div>		
						</div>
					<?php } ?>
				</div>
			<?php
		} else {
			$playlist_name = $sel_playlist['label'];
			?>
				<div class="playlist-listing-notice">
					<p><?php echo esc_html( $playlist_name ); ?> doesn't have Any Video or Series.</p>
				</div>
			<?php
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to display episode of selected series
	 */
	public function dp_selected_series_listing_filter() {
		$user_id        = get_current_user_id();
		$series_id      = filter_input( INPUT_GET, 'selected_series', FILTER_SANITIZE_STRING );
		$episodes_array = array();
		$series_term    = get_term( $series_id );
		if ( $series_term && ! is_wp_error( $series_term ) ) {
			$series_slug = $series_term->slug;
		}

		$args = array(
			'post_type'      => 'deep_pockets_videos',  // Change to your post type if necessary
			'posts_per_page' => 10, // Get all posts
			'order'          => 'ASC', // Order in ascending order.
			'orderby'        => 'post_date', // Order by term ID
			'tax_query'      => array( //phpcs:ignore
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'id',
					'terms'    => $series_id,
				),
			),
		);

		$videos_query = new \WP_Query( $args );

		// Display Videos for the Current Season
		while ( $videos_query->have_posts() ) {
			$videos_query->the_post();
			$episodes_array[] = get_the_ID();
		}

		wp_reset_postdata();

		ob_start();
		?>

		<div class="playlist-listing__series-episodes-top">
			<h2 class="playlist-listing__section-title">Episodes</h2>
			<a href="<?php echo esc_url( get_term_link( $series_term->term_id ) ); ?>" class="playlist-listing__series-link">View all</a>
		</div>
		<div class="playlist-listing__series-episodes-content">
			<?php
			if ( ! empty( $episodes_array ) ) {
				echo '<div class="playlist-listing__series-episodes-wrapper video-popup-wrapper-with-slider">';
				foreach ( $episodes_array as $episode ) {
					$video_post_id           = $episode;
					$current_user_id         = get_current_user_id();
					$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
					$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
					$main_video              = get_field( 'main_video', $video_post_id );
					$main_video_source       = array(
						'url'           => '',
						'type'          => '',
						'attachment_id' => 0,
					);

					if ( ! empty( $main_video ) && is_array( $main_video ) ) {
						$main_video_source = $this->resolve_main_video_source( $main_video );
					}

					$watch_progress       = $this->get_video_watch_progress(
						$main_video_source['attachment_id'],
						$user_id
					);
					$video_total_duration = $watch_progress['total_duration'];
					$video_watch_duration = $watch_progress['watch_duration'];
					?>
						<div class="playlist-listing__series-episode video-popup-wrapper" data-post-id="<?php echo esc_attr( $episode ); ?>">
							<div class="playlist-listing__episode-thumbnail">
								<?php
								if ( has_post_thumbnail( $episode ) ) {
									echo get_the_post_thumbnail( $episode, 'full' );
								} else {
									$placeholder_image = get_field( 'placeholder_image', 'option' );
									if ( ! empty( $placeholder_image ) ) {
										echo wp_get_attachment_image( $placeholder_image, 'full' );
									}
								}
								?>
							</div>
							<?php
							if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
								$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
								?>
									<div class="video-progress__bar">
										<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
									</div>
								<?php
							}
							?>
							<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
								<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
								<div class="video-hover-media-section">
								<?php
								$trailer_video                     = get_field( 'trailer_video', $video_post_id );
											$trailer_video_url     = '';
											$trailer_video_type    = '';
											$trailer_attachment_id = 0;

								if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
									$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
									$trailer_video_url     = $trailer_source['url'];
									$trailer_video_type    = $trailer_source['type'];
									$trailer_attachment_id = $trailer_source['attachment_id'];
								}

								if ( ! empty( $trailer_video_url ) ) {
									?>
												<video class="vjs-fluid" playsinline preload="metadata">
													<source
														src="<?php echo esc_url( $trailer_video_url ); ?>"
											<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
													>
												</video>
												<div class="media-settings">
													<div class="mute-button"></div>
												</div>
												<?php
								} else {
									if ( has_post_thumbnail( $video_post_id ) ) {
										echo get_the_post_thumbnail( $video_post_id, 'full' );
									} else {
										$placeholder_image = get_field( 'placeholder_image', 'option' );
										if ( ! empty( $placeholder_image ) ) {
											echo wp_get_attachment_image( $placeholder_image, 'full' );
										}
									}
								}
								?>
								</div>
								<div class="video-hover-content-section">
									<div class="content-top">
										<div class="video-title">
											<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
										</div>
										<div class="video-buttons">
											<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
													<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
														<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
														<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
											<?php
											if ( empty( $user_trending_video_arr ) ) {
												$user_trending_video_arr = array();
											}
											if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
												?>
													<div class="trending-now-btn">
														<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
														<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
													</div>
												<?php
											}
											?>
										</div>
									</div>
									<div class="content-bottom">
										<div class="video-description">
											<?php
												$post_content       = get_post_field( 'post_content', $video_post_id );
												$plain_text_content = wp_strip_all_tags( $post_content );
											?>
											<p><?php echo esc_html( $plain_text_content ); ?></p>
										</div>
										<div class="video-duration">
											<?php
											if ( $trailer_attachment_id > 0 ) {
												$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
												if ( ! empty( $trailer_metadata['length'] ) ) {
													?>
													<div class="duration">
														<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
													</div>
													<?php
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
				}
				echo '</div>';
			} else {
				?>
					<div class="playlist-listing-notice-fs">
						<p>This season does not have any episodes !!.</p>
					</div>
				<?php
			}
			?>
		</div>

		<?php
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to remove series from playlist
	 */
	public function dp_remove_playlist_listed_series() {
		$series_id           = filter_input( INPUT_GET, 'series_id', FILTER_SANITIZE_STRING );
		$playlist_slug       = filter_input( INPUT_GET, 'playlist_slug', FILTER_SANITIZE_STRING );
		$playlist_videos_ids = array();
		$playlist_series_ids = array();
		$user_id             = get_current_user_id();
		$user_playlists      = get_user_meta( $user_id, 'user_playlist', true );
		$updated_playlist    = array();
		if ( ! empty( $series_id ) && ! empty( $user_playlists ) ) {
			// updating playlist
			$updated_playlist = $this->dp_remove_series_id( $user_playlists, $series_id );
			update_user_meta( $user_id, 'user_playlist', $updated_playlist );
		}

		ob_start();
		if ( ! empty( $updated_playlist ) ) {
			$current_playlist = $updated_playlist[ $playlist_slug ];
			foreach ( $current_playlist['video'] as $video_id ) {
				$playlist_videos_ids[] = $video_id;
			}
			foreach ( $current_playlist['series'] as $series_id ) {
				$playlist_series_ids[] = $series_id;
			}
			if ( ! empty( $playlist_videos_ids ) || ! empty( $playlist_series_ids ) ) {
				if ( ! empty( $playlist_series_ids ) ) {
					$recent_added_formatted_series_ids = array();
					$series_ids                        = $current_playlist['series'];
					$total_series                      = count( $series_ids );
					for ( $i = $total_series - 1; $i >= 0; $i-- ) {
						$recent_added_formatted_series_ids[] = $series_ids[ $i ];
					}
					?>
					<div class="playlist-listing__series-section-wrapper">
						<h2 class="playlist-listing__section-title">Series</h2>
						<div class="playlist-listing__series-wrapper">
							<?php
							if ( $current_playlist['series'] ) {
								foreach ( $recent_added_formatted_series_ids as $series_id ) {
									$series_thumbnail = get_field( 'add_feature_image', 'video-show-category_' . $series_id );
									$series_term      = get_term( $series_id );
									$series_title     = ( $series_term && ! is_wp_error( $series_term ) ) ? $series_term->name : '';
									?>
										<div class="playlist-listing__series" data-series-id="<?php echo esc_attr( $series_id ); ?>">
											<div class="playlist-listing__remove-series" data-series-id="<?php echo esc_attr( $series_id ); ?>"></div>
											<div class="playlist-listing__series-thumbnail">
												<img src="<?php echo esc_url( $series_thumbnail ); ?>" width="266" height="372" alt="series poster"></img>
											</div>
											<div class="playlist-listing__series-content">
												<h3 class="playlist-listing__series-title"><?php echo esc_html( $series_title ); ?></h3>
											</div>
										</div>	
									<?php
								}
							}
							?>
						</div>
			
						<div class="playlist-listing__series-episodes">
							<div class="playlist-listing__series-episodes-top">
								<h2 class="playlist-listing__section-title">Episodes - vrajesh2</h2>
								<?php
									$first_series_id = $current_playlist['series'][0];
									$series_term     = get_term( $first_series_id );
								if ( $series_term && ! is_wp_error( $series_term ) ) {
									echo "<a href='" . esc_url( get_term_link( $series_term->term_id ) ) . "' class='playlist-listing__series-link'>View all</a>";
								}
								?>
							</div>
							<div class="playlist-listing__series-episodes-content">
								<?php
								$first_series_id = $current_playlist['series'][0];
								$episodes_array  = array();
								$args            = array(
									'post_type'      => 'deep_pockets_videos',
									'posts_per_page' => 10,
									'order'          => 'ASC',
									'orderby'        => 'post_date',
									'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
										array(
											'taxonomy' => 'video-show-category',
											'field'    => 'id',
											'terms'    => $series_id,
										),
									),
								);

								$videos_query = new \WP_Query( $args );

								// Display Videos for the Current Season.
								while ( $videos_query->have_posts() ) {
									$videos_query->the_post();
									$episodes_array[] = get_the_ID();
								}

								// Reset Post Data for the next loop.
								wp_reset_postdata();

								if ( ! empty( $episodes_array ) ) {
									echo '<div class="playlist-listing__series-episodes-wrapper video-popup-wrapper-with-slider">';
									foreach ( $episodes_array as $episode ) {
										$video_post_id           = $episode;
										$current_user_id         = get_current_user_id();
										$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
										$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
										$main_video              = get_field( 'main_video', $video_post_id );
										$main_video_source       = array(
											'url'  => '',
											'type' => '',
											'attachment_id' => 0,
										);

										if ( ! empty( $main_video ) && is_array( $main_video ) ) {
											$main_video_source = $this->resolve_main_video_source( $main_video );
										}

										$watch_progress       = $this->get_video_watch_progress(
											$main_video_source['attachment_id'],
											$user_id
										);
										$video_total_duration = $watch_progress['total_duration'];
										$video_watch_duration = $watch_progress['watch_duration'];
										?>
											<div class="playlist-listing__series-episode video-popup-wrapper" data-post-id="<?php echo esc_attr( $episode ); ?>">
												<div class="playlist-listing__episode-thumbnail">
													<?php
													if ( has_post_thumbnail( $episode ) ) {
														echo get_the_post_thumbnail( $episode, 'full' );
													} else {
														$placeholder_image = get_field( 'placeholder_image', 'option' );
														if ( ! empty( $placeholder_image ) ) {
															echo wp_get_attachment_image( $placeholder_image, 'full' );
														}
													}
													?>
												</div>
												<?php
												if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
													$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
													?>
														<div class="video-progress__bar">
															<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
														</div>
													<?php
												}
												?>
												<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
													<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
													<div class="video-hover-media-section">
													<?php
													$trailer_video         = get_field( 'trailer_video', $video_post_id );
													$trailer_video_url     = '';
													$trailer_video_type    = '';
													$trailer_attachment_id = 0;

													if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
														$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
														$trailer_video_url     = $trailer_source['url'];
														$trailer_video_type    = $trailer_source['type'];
														$trailer_attachment_id = $trailer_source['attachment_id'];
													}

													if ( ! empty( $trailer_video_url ) ) {
														?>
														<video class="vjs-fluid" playsinline preload="metadata">
															<source
																src="<?php echo esc_url( $trailer_video_url ); ?>"
																<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
															>
														</video>
														<div class="media-settings">
															<div class="mute-button"></div>
														</div>
														<?php
													} else {
														if ( has_post_thumbnail( $video_post_id ) ) {
															echo get_the_post_thumbnail( $video_post_id, 'full' );
														} else {
															$placeholder_image = get_field( 'placeholder_image', 'option' );
															if ( ! empty( $placeholder_image ) ) {
																echo wp_get_attachment_image( $placeholder_image, 'full' );
															}
														}
													}
													?>
													</div>
													<div class="video-hover-content-section">
														<div class="content-top">
															<div class="video-title">
																<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
															</div>
															<div class="video-buttons">
																<div class="play-from-beginning" data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
																	<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
																		data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
																		<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
																			data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
																		<?php endif; ?>
																		<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
																			data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
																		<?php endif; ?>
																	<?php endif; ?>
																></div>
																<?php
																if ( empty( $user_trending_video_arr ) ) {
																	$user_trending_video_arr = array();
																}
																if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
																	?>
																		<div class="trending-now-btn">
																			<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
																			<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
																		</div>
																	<?php
																}
																?>
															</div>
														</div>
														<div class="content-bottom">
															<div class="video-description">
																<?php
																	$post_content       = get_post_field( 'post_content', $video_post_id );
																	$plain_text_content = wp_strip_all_tags( $post_content );
																?>
																<p><?php echo esc_html( $plain_text_content ); ?></p>
															</div>
															<div class="video-duration">
																<?php
																if ( $trailer_attachment_id > 0 ) {
																	$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
																	if ( ! empty( $trailer_metadata['length'] ) ) {
																		?>
																		<div class="duration">
																			<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
																		</div>
																		<?php
																	}
																}
																?>
															</div>
														</div>
													</div>
												</div>
											</div>
										<?php
									}
									echo '</div>';
								} else {
									?>
										<div class="playlist-listing-notice-fs">
											<p>This season does not have any episodes !!.</p>
										</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				$playlist_name = $current_playlist['label'];
				?>
					<div class="playlist-listing-notice">
						<p><?php echo esc_html( $playlist_name ); ?> doesn't have Any Video or Series.</p>
					</div>
				<?php
			}
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to remove video from playlist.
	 */
	public function dp_remove_playlist_listed_video() {
		$post_id             = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_STRING );
		$playlist_slug       = filter_input( INPUT_GET, 'playlist_slug', FILTER_SANITIZE_STRING );
		$playlist_videos_ids = array();
		$playlist_series_ids = array();
		$user_id             = get_current_user_id();
		$user_playlists      = get_user_meta( $user_id, 'user_playlist', true );
		$recently_added      = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
		$updated_playlist    = array();
		$response_array      = array();
		if ( ! empty( $post_id ) && ! empty( $user_playlists ) ) {
			// updating playlist
			$updated_playlist = $this->dp_remove_post_id( $user_playlists, $post_id );
			update_user_meta( $user_id, 'user_playlist', $updated_playlist );

			// updating recent added video list
			$index = array_search( $post_id, $recently_added, true );
			if ( $index !== false ) {
				unset( $recently_added[ $index ] );
				$recently_added = array_values( $recently_added );
			}
			update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
		}

		ob_start();
		if ( ! empty( $updated_playlist ) ) {
			$current_playlist = $updated_playlist[ $playlist_slug ];
			foreach ( $current_playlist['video'] as $video_id ) {
				$playlist_videos_ids[] = $video_id;
			}
			foreach ( $current_playlist['series'] as $series_id ) {
				$playlist_series_ids[] = $series_id;
			}
			if ( ! empty( $playlist_videos_ids ) || ! empty( $playlist_series_ids ) ) {
				$playlist_videos = $updated_playlist[ $playlist_slug ]['video'];
				if ( ! empty( $playlist_videos_ids ) ) {
					?>
					<div class="playlist-listing__video-top">
						<h2 class="playlist-listing__section-title"><?php echo esc_html( $current_playlist['label'] ); ?></h2>
						<?php if ( count( $playlist_videos ) > 6 ) { ?>
							<a href="<?php echo esc_url( home_url( '/playlist-listing/?playlist=' . $current_playlist['slug'] ) ); ?>" class="playlist-listing__video-link">View all</a>
						<?php } ?>	
					</div>
					<div class="playlist-listing__video-wrapper video-popup-wrapper-with-slider">
						<?php
						foreach ( $updated_playlist[ $playlist_slug ]['video'] as $video_id ) {
							$video_post_id           = $video_id;
							$current_user_id         = get_current_user_id();
							$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
							$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

							$main_video        = get_field( 'main_video', $video_post_id );
							$main_video_source = array(
								'url'           => '',
								'type'          => '',
								'attachment_id' => 0,
							);

							if ( ! empty( $main_video ) && is_array( $main_video ) ) {
								$main_video_source = $this->resolve_main_video_source( $main_video );
							}

							$watch_progress       = $this->get_video_watch_progress(
								$main_video_source['attachment_id'],
								$user_id
							);
							$video_total_duration = $watch_progress['total_duration'];
							$video_watch_duration = $watch_progress['watch_duration'];
							?>
							<div class="playlist-listing__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
								<div class="playlist-listing__video-thumbnail">
									<?php
									if ( has_post_thumbnail( $video_id ) ) {
										echo get_the_post_thumbnail( $video_id, 'full' );
									} else {
										$placeholder_image = get_field( 'placeholder_image', 'option' );
										if ( ! empty( $placeholder_image ) ) {
											echo wp_get_attachment_image( $placeholder_image, 'full' );
										}
									}
									?>
								</div>
								<?php
								if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
									$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
									?>
										<div class="video-progress__bar">
											<div class="video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
										</div>
									<?php
								}
								?>
								
								<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
									<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
									<div class="video-hover-media-section">
									<?php
									$trailer_video                 = get_field( 'trailer_video', $video_post_id );
											$trailer_video_url     = '';
											$trailer_video_type    = '';
											$trailer_attachment_id = 0;

									if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
										$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
										$trailer_video_url     = $trailer_source['url'];
										$trailer_video_type    = $trailer_source['type'];
										$trailer_attachment_id = $trailer_source['attachment_id'];
									}

									if ( ! empty( $trailer_video_url ) ) {
										?>
										<video class="vjs-fluid" playsinline preload="metadata">
											<source
												src="<?php echo esc_url( $trailer_video_url ); ?>"
										<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
											>
										</video>
										<div class="media-settings">
											<div class="mute-button"></div>
										</div>
										<?php
									} else {
										if ( has_post_thumbnail( $video_post_id ) ) {
											echo get_the_post_thumbnail( $video_post_id, 'full' );
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
									}
									?>
									</div>
									<div class="video-hover-content-section">
										<div class="content-top">
											<div class="video-title">
												<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
											</div>
											<div class="video-buttons">
												<div
													class="play-from-beginning"
													data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
													<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
														data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
														<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
															data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
														<?php endif; ?>
														<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													<?php endif; ?>
												></div>
												<?php
												if ( is_user_logged_in() ) {
													?>
														<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
															<div class="tooltip-text" style="display:none"></div>
															<?php
															if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
																?>
																	<div class="added-to-playlist pl-icon"></div>
																<?php
															} else {
																?>
																	<div class="not-added-to-playlist pl-icon"></div>
																<?php
															}
															?>
														</div>
													<?php
												}

												if ( empty( $user_trending_video_arr ) ) {
													$user_trending_video_arr = array();
												}
												if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
													?>
														<div class="trending-now-btn">
															<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
															<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
														</div>
													<?php
												}
												?>
											</div>
										</div>
										<div class="content-bottom">
											<div class="video-description">
												<?php
													$post_content       = get_post_field( 'post_content', $video_post_id );
													$plain_text_content = wp_strip_all_tags( $post_content );
												?>
												<p><?php echo esc_html( $plain_text_content ); ?></p>
											</div>
											<div class="video-duration">
												<?php
												if ( $trailer_attachment_id > 0 ) {
													$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
													if ( ! empty( $trailer_metadata['length'] ) ) {
														?>
														<div class="duration">
															<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
														</div>
														<?php
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>		
							<?php
						}
						?>
					</div>
					<?php
				}
			} else {
				$playlist_name = $current_playlist['label'];
				?>
					<div class="playlist-listing-notice">
						<p><?php echo esc_html( $playlist_name ); ?> doesn't have Any Video or Series.</p>
					</div>
				<?php
			}
		}
		$playlist_video_html = ob_get_clean();

		ob_start();
		$updated_recent_array = array();
		$total_videos         = count( $recently_added );

		// Iterate over the array in reverse order
		for ( $i = $total_videos - 1; $i >= 0; $i-- ) {
			$updated_recent_array[] = $recently_added[ $i ];
		}

		foreach ( $updated_recent_array as $index => $video_id ) {
			if ( $index < 4 ) {
				$video_post_id           = $video_id;
				$current_user_id         = get_current_user_id();
				$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
				$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
				$trailer_video           = get_field( 'trailer_video', $video_post_id );
				$trailer_video_url       = '';
				$trailer_video_type      = '';
				$trailer_attachment_id   = 0;

				if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
					$trailer_source        = $this->resolve_trailer_video_source( $trailer_video );
					$trailer_video_url     = $trailer_source['url'];
					$trailer_video_type    = $trailer_source['type'];
					$trailer_attachment_id = $trailer_source['attachment_id'];
				}

				$main_video        = get_field( 'main_video', $video_post_id );
				$main_video_source = array(
					'url'           => '',
					'type'          => '',
					'attachment_id' => 0,
				);

				if ( ! empty( $main_video ) && is_array( $main_video ) ) {
					$main_video_source = $this->resolve_main_video_source( $main_video );
				}
				?>
					<div class="recently-added__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $video_id ); ?>">
						<?php
						if ( has_post_thumbnail( $video_id ) ) {
							echo get_the_post_thumbnail( $video_id, 'full' );
						} else {
							$placeholder_image = get_field( 'placeholder_image', 'option' );
							if ( ! empty( $placeholder_image ) ) {
								echo wp_get_attachment_image( $placeholder_image, 'full' );
							}
						}
						?>
						<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
							<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
							<div class="video-hover-media-section">
							<?php
							if ( ! empty( $trailer_video_url ) ) {
								?>
								<video class="vjs-fluid" playsinline preload="metadata">
									<source
										src="<?php echo esc_url( $trailer_video_url ); ?>"
										<?php echo ! empty( $trailer_video_type ) ? 'type="' . esc_attr( $trailer_video_type ) . '"' : ''; ?>
									>
								</video>
								<div class="media-settings">
									<div class="mute-button"></div>
								</div>
								<?php
							} else {
								if ( has_post_thumbnail( $video_post_id ) ) {
									echo get_the_post_thumbnail( $video_post_id, 'full' );
								} else {
									$placeholder_image = get_field( 'placeholder_image', 'option' );
									if ( ! empty( $placeholder_image ) ) {
										echo wp_get_attachment_image( $placeholder_image, 'full' );
									}
								}
							}
							?>
							</div>
							<div class="video-hover-content-section">
								<div class="content-top">
									<div class="video-title">
										<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
									</div>
									<div class="video-buttons">
										<div
											class="play-from-beginning"
											data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
											<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
												data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
												<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
													data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
												<?php endif; ?>
												<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
													data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
												<?php endif; ?>
											<?php endif; ?>
										></div>
										<?php
										if ( is_user_logged_in() ) {
											?>
												<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
													<div class="tooltip-text" style="display:none"></div>
													<?php
													if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
														?>
															<div class="added-to-playlist pl-icon"></div>
														<?php
													} else {
														?>
															<div class="not-added-to-playlist pl-icon"></div>
														<?php
													}
													?>
												</div>
											<?php
										}

										if ( empty( $user_trending_video_arr ) ) {
											$user_trending_video_arr = array();
										}
										if ( ! in_array( $video_post_id, $user_trending_video_arr ) ) {
											?>
												<div class="trending-now-btn">
													<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
													<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
												</div>
											<?php
										}
										?>
									</div>
								</div>
								<div class="content-bottom">
									<div class="video-description">
										<?php
											$post_content       = get_post_field( 'post_content', $video_post_id );
											$plain_text_content = wp_strip_all_tags( $post_content );
										?>
										<p><?php echo esc_html( $plain_text_content ); ?></p>
									</div>
									<div class="video-duration">
										<?php
										if ( $trailer_attachment_id > 0 ) {
											$trailer_metadata = wp_get_attachment_metadata( $trailer_attachment_id );
											if ( ! empty( $trailer_metadata['length'] ) ) {
												?>
												<div class="duration">
													<?php echo esc_html( format_video_duration( (int) $trailer_metadata['length'] ) ); ?>
												</div>
												<?php
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		}
		$recently_added_video_html = ob_get_clean();

		$response_array['playlist_video_html']       = $playlist_video_html;
		$response_array['recently_added_video_html'] = $recently_added_video_html;
		echo json_encode($response_array); //phpcs:ignore
		wp_die();
	}

	/**
	 * Function to remove post id from playlist.
	 * 
	 * @param array $array Array of playlists.
	 * @param int $value Value of the post id.
	 * @return array Array of playlists.
	 */
	public function dp_remove_post_id( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $key => $subArray ) {
				if ( is_array( $subArray ) && isset( $subArray['video'] ) && is_array( $subArray['video'] ) ) {
					$filteredVideo = array_filter( $subArray['video'], 'is_numeric' );
					if ( ! empty( $filteredVideo ) ) {
						$index = array_search( $value, $subArray['video'], true );
						if ( $index !== false ) {
							unset( $array[ $key ]['video'][ $index ] );
							$array[ $key ]['video'] = array_values( $array[ $key ]['video'] );
						}
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Function to remove post id from playlist.
	 * 
	 * @param array $array Array of playlists.
	 * @param int $value Value of the post id.
	 * @return array Array of playlists.
	 */
	public function dp_remove_series_id( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $key => $subArray ) {
				if ( is_array( $subArray ) && isset( $subArray['series'] ) && is_array( $subArray['series'] ) ) {
					if ( ! empty( $subArray['series'] ) ) {
						$index = array_search( $value, $subArray['series'], true );
						if ( $index !== false ) {
							unset( $array[ $key ]['series'][ $index ] );
							$array[ $key ]['series'] = array_values( $array[ $key ]['series'] );
						}
					}
				}
			}
		}
		return $array;
	}
}
