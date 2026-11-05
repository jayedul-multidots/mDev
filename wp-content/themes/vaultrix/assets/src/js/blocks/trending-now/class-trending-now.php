<?php
/**
 * Registers the vaultrix/trending-now block.
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
 *  Class for the vaultrix/trending-now block.
 */
class Trending_Now extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'trending-now';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {}

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
				'class' => 'trending-now-section',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading              = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$heading_style        = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$selected_categories  = ! empty( $attributes['selectedCategory'] ) ? $attributes['selectedCategory'] : '';
		$trending_videos_list = get_option( 'trending_videos', array() );

		// Sort the array in descending order based on counts
		arsort( $trending_videos_list );

		// Extract the keys (video_ids) from the sorted array
		$highest_counts_video_ids = array_keys( $trending_videos_list );

		$query_args = array(
			'posts_per_page' => 4,
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
		);

		if ( ! empty( $highest_counts_video_ids ) ) {
			$query_args['post__in'] = $highest_counts_video_ids;
			$query_args['orderby']  = 'post__in';
		} else {
			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = 'asc';
		}

		if ( ! empty( $selected_categories ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				),
			);
		}

		$query = new \WP_Query( $query_args );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="container">
				<div class="trending-now-section__heading-section">
					<?php if ( ! empty( $heading ) ) : ?>
						<h3 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
				</div>
				<div class="trending-now-section__video-series-wrapper video-popup-wrapper-with-slider">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$video_post_id           = get_the_ID();
						$current_user_id         = get_current_user_id();
						$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
						$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );
						$trailer_video           = get_field( 'trailer_video', $video_post_id );
						$trailer_video_url       = '';
						$trailer_video_type      = '';
						$trailer_attachment_id   = 0;

						if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
							$trailer_video_url     = $trailer_video['url'] ?? '';
							$trailer_video_type    = $trailer_video['mime_type'] ?? '';
							$trailer_attachment_id = $trailer_video['ID'] ?? ( $trailer_video['id'] ?? 0 );
							$trailer_attachment_id = (int) $trailer_attachment_id;

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
							<div class="trending-now-section__video-series video-popup-wrapper" data-post-id='<?php echo esc_attr( $video_post_id ); ?>'>
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
								<div class="trending-now-section__video-series-overlay">
									<img src="/wp-content/themes/vaultrix/assets/src/js/blocks/trending-now/Logo.png"></img>
									<h3><?php echo esc_html( get_the_title() ); ?></h3>
								</div>

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
													<?php if ( ! empty( $trailer_video_url ) ) : ?>
														data-trailer-url="<?php echo esc_url( $trailer_video_url ); ?>"
														<?php if ( ! empty( $trailer_video_type ) ) : ?>
															data-trailer-type="<?php echo esc_attr( $trailer_video_type ); ?>"
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
					?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();

	}
}
