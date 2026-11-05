<?php
/**
 * Template part for displaying archive videos
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

if ( 'deep_pockets_videos' === get_post_type() ) :
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

		$main_video        = get_field( 'main_video', $video_post_id );
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

		$video_total_duration = '';
		$video_watch_duration = '';
		$video_key            = $main_video_source['attachment_id'] > 0 ? (string) $main_video_source['attachment_id'] : '';
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

		if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
			$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
			?>
				<div class="video-listing-slider__duration-bar video-progress__bar">
					<div class="video-listing-slider__duration-bar-inner video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
				</div>
			<?php
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
endif;
