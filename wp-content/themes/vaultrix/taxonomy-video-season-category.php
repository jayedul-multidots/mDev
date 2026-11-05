<?php
/**
 * The template for displaying season wise archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

get_header();
?>

	<main id="primary" class="site-main vaultrix-archive-main vaultrix-main">
		<?php
		$current_user_id                = get_current_user_id();
		$series_slug_for_season_archive = filter_input( INPUT_GET, 'series_slug', FILTER_SANITIZE_STRING );
		$current_series_data            = get_term_by( 'slug', $series_slug_for_season_archive, 'video-show-category' );
		$current_series_id              = $current_series_data->term_id;
		if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
			if ( have_posts() ) {
				$video_term = get_queried_object();

				if ( $video_term instanceof WP_Term ) {
					$video_term_name              = $video_term->name; // Outputs the name of the current term
					$video_banner_url             = get_field( 'add_banner_image', 'video-show-category_' . esc_attr( $current_series_id ) );
					$video_feature_url            = get_field( 'add_feature_image', 'video-show-category_' . esc_attr( $current_series_id ) );
					$banner_placeholder_image     = get_field( 'placeholder_image', 'option' );
					$banner_placeholder_image_url = wp_get_attachment_image_url( $banner_placeholder_image, 'full' );
					$video_img_url                = ! empty( $video_banner_url ) ? $video_banner_url : $video_feature_url;
					$term_trailer_video           = get_field( 'term_trailer_video', 'video-show-category_' . esc_attr( $current_series_id ) );
					$term_trailer_video_url       = '';
					$term_trailer_video_type      = '';
					$show_banner_url              = ! empty( $video_img_url ) ? $video_img_url : $banner_placeholder_image_url;

					if ( ! empty( $term_trailer_video ) && is_array( $term_trailer_video ) ) {
						$term_trailer_attachment_id = (int) ( $term_trailer_video['ID'] ?? ( $term_trailer_video['id'] ?? 0 ) );
						$term_trailer_video_url     = $term_trailer_video['url'] ?? '';
						$term_trailer_video_type    = $term_trailer_video['mime_type'] ?? '';

						if ( $term_trailer_attachment_id > 0 ) {
							$attachment_url = wp_get_attachment_url( $term_trailer_attachment_id );

							if ( $attachment_url ) {
								$term_trailer_video_url = $attachment_url;
							}

							if ( empty( $term_trailer_video_type ) ) {
								$term_trailer_video_type = get_post_mime_type( $term_trailer_attachment_id ) ?: '';
							}
						}
					}
				}
				?>
				<div class="archive-banner">
					<div class="archive-banner__caption">
						<div class="archive-banner__content container">
							<h1 class="archive-banner__heading">Watch All the Episodes of <br><span><?php echo esc_html( $video_term_name ); ?></span></h1>
							<p class="archive-banner__desc"><?php echo esc_html( $video_term->description ); ?></p>
						</div>
						<div class="archive-banner__media">
							<?php
							if ( ! empty( $term_trailer_video_url ) ) {
								?>
								<div class="series-banner-img">
									<img src="<?php echo esc_url( $show_banner_url ); ?>" alt="banner" width="1400" height="600">
								</div>
								<video class="vjs-fluid" playsinline preload="metadata">
									<source
										src="<?php echo esc_url( $term_trailer_video_url ); ?>"
										<?php echo ! empty( $term_trailer_video_type ) ? 'type="' . esc_attr( $term_trailer_video_type ) . '"' : ''; ?>
									>
								</video>
								<div class="media-settings">
									<div class="mute-button"></div>
								</div>
								<?php
							} else {
								?>
									<img src="<?php echo esc_url( $show_banner_url ); ?>" alt="banner" width="1400" height="600">
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<h3 class="archive-video-listing-title container">Recently Added</h3>
				<div class="archive-videos-wrapper container">
					<div class="archive-videos-inner-wrapper">
					<?php
					if ( $series_slug_for_season_archive !== null ) {
						$season_archive_videos = new WP_Query(
							array(
								'post_type' => 'deep_pockets_videos',
								'order'     => 'ASC', // Order in ascending order.
								'orderby'   => 'post_date', // Order by term ID
								'tax_query' => array( // phpcs:ignore
									'relation' => 'AND', // Add an 'AND' relation between the conditions
									array(
										'taxonomy' => 'video-season-category',
										'field'    => 'term_id',
										'terms'    => $video_term->term_id,
									),
									array(
										'taxonomy' => 'video-show-category',
										'field'    => 'slug',
										'terms'    => $series_slug_for_season_archive,
									),
								),
							)
						);
					} else {
						$season_archive_videos = new WP_Query(
							array(
								'post_type' => 'deep_pockets_videos',
								'order'     => 'ASC', // Order in ascending order.
								'orderby'   => 'post_date', // Order by term ID
								'tax_query' => array( // phpcs:ignore
									'relation' => 'AND', // Add an 'AND' relation between the conditions
									array(
										'taxonomy' => 'video-season-category',
										'field'    => 'term_id',
										'terms'    => $video_term->term_id,
									),
									array(
										'taxonomy' => 'video-show-category',
										'field'    => 'slug',
										'terms'    => 'series',
									),
								),
							)
						);

					}
					if ( $season_archive_videos->have_posts() ) {
						while ( $season_archive_videos->have_posts() ) {
							$season_archive_videos->the_post();
							?>
								<div class="archive-videos-inner-wrapper__thumbnail video-popup-wrapper">
									<div class="archive-videos-inner-wrapper__thumbnail-wrapper">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail(
												'post-thumbnail',
												array(
													'alt' => the_title_attribute(
														array(
															'echo' => false,
														)
													),
												)
											);
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
										?>
									</div>
									<?php

									$video_post_id           = get_the_ID();
									$current_user_id         = get_current_user_id();
									$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
									$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

									$video_total_duration = '';
									$video_watch_duration = '';

									$main_video        = get_field( 'main_video', get_the_ID() );
									$main_video_source = array(
										'url'           => '',
										'type'          => '',
										'attachment_id' => 0,
									);

									if ( ! empty( $main_video ) && is_array( $main_video ) ) {
										$main_video_source['url']           = $main_video['url'] ?? '';
										$main_video_source['type']          = $main_video['mime_type'] ?? '';
										$main_video_source['attachment_id'] = (int) ( $main_video['ID'] ?? ( $main_video['id'] ?? 0 ) );

										if ( $main_video_source['attachment_id'] > 0 ) {
											$attachment_url = wp_get_attachment_url( $main_video_source['attachment_id'] );

											if ( $attachment_url ) {
												$main_video_source['url'] = $attachment_url;
											}

											if ( empty( $main_video_source['type'] ) ) {
												$main_video_source['type'] = get_post_mime_type( $main_video_source['attachment_id'] ) ?: '';
											}
										}
									}

									$video_key              = $main_video_source['attachment_id'] > 0 ? (string) $main_video_source['attachment_id'] : '';
									$bc_video_duration_meta = get_user_meta( $current_user_id, 'bc_video_duration_time', true );

									if ( ! empty( $bc_video_duration_meta ) && ! empty( $video_key ) ) {
										if ( array_key_exists( $video_key, $bc_video_duration_meta ) ) {
											$video_total_duration = $bc_video_duration_meta[ $video_key ];
										}
									}

									$bc_video_watch_duration = get_user_meta( $current_user_id, 'bc_watch_time', true );

									if ( ! empty( $bc_video_watch_duration ) && ! empty( $video_key ) ) {
										if ( array_key_exists( $video_key, $bc_video_watch_duration ) ) {
											$video_watch_duration = $bc_video_watch_duration[ $video_key ];
										}
									}

									$trailer_video         = get_field( 'trailer_video', $video_post_id );
									$trailer_video_url     = '';
									$trailer_video_type    = '';
									$trailer_attachment_id = 0;

									if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
										$trailer_video_url     = $trailer_video['url'] ?? '';
										$trailer_video_type    = $trailer_video['mime_type'] ?? '';
										$trailer_attachment_id = (int) ( $trailer_video['ID'] ?? ( $trailer_video['id'] ?? 0 ) );

										if ( $trailer_attachment_id > 0 ) {
											$attachment_url = wp_get_attachment_url( $trailer_attachment_id );

											if ( $attachment_url ) {
												$trailer_video_url = $attachment_url;
											}

											if ( empty( $trailer_video_type ) ) {
												$trailer_video_type = get_post_mime_type( $trailer_attachment_id ) ?: '';
											}
										}
									}

									if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
										$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
										?>
											<div class="video-listing-slider__duration-bar video-progress__bar">
												<div class="video-listing-slider__duration-bar-inner video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
											</div>
										<?php
									}
									?>
									<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
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
											<?php
										} else {
											if ( has_post_thumbnail() ) {
												the_post_thumbnail( 'full' );
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
													<h3 class="title"><?php echo esc_html( get_the_title() ); ?></h3>
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
													<?php the_content(); ?>
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
				<?php
			} else {
				get_template_part( 'template-parts/content', 'none' );
			}
			?>
				</div>
			<?php
		} else {
			if ( is_user_logged_in() ) {
				$pmpro_restriction_text = get_field( 'pmpro_restriction_non_member_user_text', 'option' );
			} else {
				$pmpro_restriction_text = get_field( 'pmpro_restriction_non_login_user_text', 'option' );
			}
			?>
			<div class="dp-restricted-section-message">
				<div class="container">
					<h1 class="member-warning-message"><?php echo esc_html( $pmpro_restriction_text ); ?></h1>
					<h2 class="member-warning-message">New to this site? <span>Register Now</span></h2>
				</div>
			</div>
			<?php
		}
		?>
	</main><!-- #main -->

<?php
get_footer();
