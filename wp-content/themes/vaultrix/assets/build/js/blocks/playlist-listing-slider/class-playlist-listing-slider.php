<?php
/**
 * Registers the vaultrix/playlist-listing-slider block.
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
 *  Class for the vaultrix/playlist-listing-slider block.
 */
class Playlist_Listing_Slider extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'playlist-listing-slider';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {}

	/**
	 * Resolve the video source URL and mime type from an ACF file field.
	 *
	 * @param array $acf_video ACF file field array.
	 * @return array{url: string, type: string, attachment_id: int}
	 */
	private function resolve_video_source( array $acf_video ): array {
		$video_url     = $acf_video['url'] ?? '';
		$video_type    = $acf_video['mime_type'] ?? '';
		$attachment_id = $acf_video['ID'] ?? ( $acf_video['id'] ?? 0 );
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
				'class' => 'playlist-listing-slider',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$seeAllLinkText    = isset( $attributes['seeAllLinkText'] ) ? $attributes['seeAllLinkText'] : '';
		$seeAllLink        = isset( $attributes['seeAllLink'] ) ? $attributes['seeAllLink'] : '';
		$secbg_color       = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#001e4e';
		$display_autoslide = ! empty( $attributes['displayAutoslide'] ) ? $attributes['displayAutoslide'] : false;
		$display_arrows    = ! empty( $attributes['displayArrows'] ) ? $attributes['displayArrows'] : false;
		$number_of_posts   = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 5;

		$heading_style  = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$style_sec_attr = 'background: ' . $secbg_color . ';';

		$user_id            = get_current_user_id();
		$user_playlist_meta = get_user_meta( $user_id, 'user_playlist', true );

		if ( ! is_array( $user_playlist_meta ) ) {
			$user_playlist_meta = array();
		}

		$all_video_ids        = array();
		$final_video_id_array = array();

		// Loop through each playlist (e.g., 'forex' and 'secure-the-swag')
		foreach ( $user_playlist_meta as $playlist_key => $playlist_data ) {

			// Access the 'video' array values or perform other operations for each playlist
			if ( isset( $playlist_data['video'] ) && is_array( $playlist_data['video'] ) ) {
				$all_video_ids[ $playlist_key ] = $playlist_data['video'];
			}
		}

		// Loop through all collected video IDs for each playlist
		foreach ( $all_video_ids as $playlist_key => $video_array ) {
			foreach ( $video_array as $video_id ) {
				$final_video_id_array[] = $video_id;
			}
		}

		$query_args = array(
			'posts_per_page' => $number_of_posts,
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
		);

		if ( ! empty( $final_video_id_array ) ) {
			$query_args['post__in'] = $final_video_id_array;
			$query_args['orderby']  = 'post__in';
		} else {
			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = 'asc';
		}

		$query = new \WP_Query( $query_args );

		ob_start();
		?>
		<div 
			<?php echo wp_kses_post( $wrapper_attributes ); ?>
			style="<?php echo esc_attr( $style_sec_attr ); ?>"
			data-block-auto-slide="<?php echo esc_attr( $display_autoslide ? 'true' : 'false' ); ?>"
			data-display-arrows="<?php echo esc_attr( $display_arrows ? 'true' : 'false' ); ?>"
		>
			<div class="container">
				<div class="playlist-listing-slider__heading-section">
					<?php if ( ! empty( $heading ) ) : ?>
						<h3 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $final_video_id_array ) ) : ?>
						<?php if ( $attributes['showSeeAllLink'] && ! empty( $seeAllLink ) && ! empty( $seeAllLinkText ) ) : ?>
							<a href="<?php echo esc_url( $seeAllLink ); ?>" ><p><?php echo esc_html( $seeAllLinkText ); ?></p></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $final_video_id_array ) ) { ?>
					<div class="playlist-listing-slider__boxes video-popup-wrapper-with-slider">
						<?php
						while ( $query->have_posts() ) {
							$query->the_post();

							$video_post_id           = get_the_ID();
							$current_user_id         = get_current_user_id();
							$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
							$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
							$trailer_video           = get_field( 'trailer_video', $video_post_id );
							$main_video              = get_field( 'main_video', $video_post_id );
							$trailer_video_source    = array(
								'url'           => '',
								'type'          => '',
								'attachment_id' => 0,
							);
							$main_video_source       = array(
								'url'           => '',
								'type'          => '',
								'attachment_id' => 0,
							);

							if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
								$trailer_video_source = $this->resolve_video_source( $trailer_video );
							}

							if ( ! empty( $main_video ) && is_array( $main_video ) ) {
								$main_video_source = $this->resolve_video_source( $main_video );
							}
							?>
							<div class="playlist-listing-slider__box video-popup-wrapper" id='video-modal' data-post-id='<?php echo esc_attr( get_the_ID() ); ?>'>
								<div class="playlist-listing-slider__box-img">
									<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'full' );
									} else {
										$placeholder_image = get_field( 'placeholder_image', 'option' );
										if ( ! empty( $placeholder_image ) ) {
											echo wp_get_attachment_image( $placeholder_image, 'full' );
										}
									}
									?>
								</div>
								<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
									<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
									<div class="video-hover-media-section">
									<?php
									if ( ! empty( $trailer_video_source['url'] ) ) {
										?>
										<video class="vjs-fluid" playsinline preload="metadata">
											<source
												src="<?php echo esc_url( $trailer_video_source['url'] ); ?>"
												<?php echo ! empty( $trailer_video_source['type'] ) ? 'type="' . esc_attr( $trailer_video_source['type'] ) . '"' : ''; ?>
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
												if ( $main_video_source['attachment_id'] > 0 ) {
													$main_video_metadata = wp_get_attachment_metadata( $main_video_source['attachment_id'] );
													if ( ! empty( $main_video_metadata['length'] ) ) {
														?>
														<div class="duration">
															<?php echo esc_html( format_video_duration( (int) $main_video_metadata['length'] ) ); ?>
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
				} else {
					?>
					<div class="no-article-found-div">
						<p><?php esc_html_e( 'No Videos Found In Your Playlist.', 'vaultrix' ); ?></p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
