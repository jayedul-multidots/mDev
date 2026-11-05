<?php
/**
 * Registers the vaultrix/recently-added-video-listing block.
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
use WP_REST_Request;
use WP_REST_Response;

/**
 *  Class for the vaultrix/recently-added-video-listing block.
 */
class Recently_Added_Video_Listing extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'recently-added-video-listing';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_api_endpoints' ) );
	}

	/**
	 * Register REST endpoints for the block editor.
	 *
	 * @return void
	 */
	public function register_api_endpoints() {
		register_rest_route(
			'vaultrix/v1',
			'/recently-added-video-listing/videos',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_videos_by_taxonomy' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Build query args for fetching videos by taxonomy.
	 *
	 * @param string $video_show_category Parent category slug.
	 * @param string $video_sub_category  Sub category slug.
	 * @return array
	 */
	private function build_videos_query_args( string $video_show_category, string $video_sub_category = '' ): array {
		$query_args = array(
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'asc',
		);

		if ( ! empty( $video_show_category ) && empty( $video_sub_category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $video_show_category,
				),
			);
		}

		if ( ! empty( $video_show_category ) && ! empty( $video_sub_category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				'relation' => 'AND',
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $video_show_category,
				),
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $video_sub_category,
				),
			);
		}

		return $query_args;
	}

	/**
	 * Fetch videos for the block editor based on selected taxonomy terms.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function get_videos_by_taxonomy( WP_REST_Request $request ): WP_REST_Response {
		$video_show_category = sanitize_text_field( (string) $request->get_param( 'video_show_category' ) );
		$video_sub_category  = sanitize_text_field( (string) $request->get_param( 'video_sub_category' ) );
		$result              = array();

		if ( empty( $video_show_category ) ) {
			return new WP_REST_Response( $result, 200 );
		}

		$query = new \WP_Query( $this->build_videos_query_args( $video_show_category, $video_sub_category ) );

		if ( $query->have_posts() ) {
			$i = 0;

			while ( $query->have_posts() ) {
				$query->the_post();

				$result[ $i ]['id']    = get_the_ID();
				$result[ $i ]['title'] = get_the_title();

				++$i;
			}

			wp_reset_postdata();
		}

		return new WP_REST_Response( $result, 200 );
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

		$classes = 'recently-added-video-listing';

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading               = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$seeAllLinkText        = isset( $attributes['seeAllLinkText'] ) ? $attributes['seeAllLinkText'] : '';
		$seeAllLink            = isset( $attributes['seeAllLink'] ) ? $attributes['seeAllLink'] : '';
		$selectedvideos        = ! empty( $attributes['selectedVideoIDs'] ) ? json_decode( $attributes['selectedVideoIDs'] ) : array();
		$selectedvideos_array  = ! empty( $selectedvideos ) ? wp_list_pluck( $selectedvideos, 'value' ) : array();
		$selected_categories   = ! empty( $attributes['selectedCategory'] ) ? $attributes['selectedCategory'] : '';
		$selected_sub_category = ! empty( $attributes['selectedSubCategory'] ) ? $attributes['selectedSubCategory'] : '';
		$secbg_color           = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#001e4e';
		$display_autoslide     = ! empty( $attributes['displayAutoslide'] ) ? $attributes['displayAutoslide'] : false;
		$display_arrows        = ! empty( $attributes['displayArrows'] ) ? $attributes['displayArrows'] : false;
		$number_of_posts       = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 5;

		$heading_style  = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$style_sec_attr = 'background: ' . $secbg_color . ';';

		$query_args = array(
			'posts_per_page' => $number_of_posts,
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
		);

		if ( ! empty( $selected_categories ) && empty( $selected_sub_category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				),
			);
		}

		if ( ! empty( $selected_categories ) && ! empty( $selected_sub_category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				'relation' => 'AND',
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				),
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_sub_category,
				),
			);
		}

		if ( ! empty( $selectedvideos_array ) ) {
			$query_args['post__in'] = $selectedvideos_array;
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
				<div class="recently-added-video-listing__heading-section">
					<?php if ( ! empty( $heading ) ) : ?>
						<h3 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
					<?php if ( $attributes['showSeeAllLink'] && ! empty( $seeAllLink ) && ! empty( $seeAllLinkText ) ) : ?>
						<a href="<?php echo esc_url( $seeAllLink ); ?>" ><p><?php echo esc_html( $seeAllLinkText ); ?></p></a>
						<?php
					endif;
					?>
				</div>
				<?php if ( $query->have_posts() ) { ?>
					<div class="recently-added-video-listing__boxes video-popup-wrapper-with-slider">
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
							<div class="recently-added-video-listing__box video-popup-wrapper">
								<div class="recently-added-video-listing__box-img">
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
					<?php
				} else {
					?>
					<div class="no-article-found-div">
						<p><?php esc_html_e( 'No Videos Found', 'vaultrix' ); ?></p>
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
