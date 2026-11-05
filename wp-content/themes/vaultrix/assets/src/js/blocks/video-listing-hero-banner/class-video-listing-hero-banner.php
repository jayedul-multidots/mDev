<?php
/**
 * Registers the vaultrix/video-listing-hero-banner block.
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
use WP_REST_Response;

/**
 *  Class for the vaultrix/video-listing-hero-banner block.
 */
class Video_Listing_Hero_Banner extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'video-listing-hero-banner';
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
			'/video-listing-hero-banner/videos',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_all_videos' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get all published videos for the block editor video picker.
	 *
	 * @return WP_REST_Response
	 */
	public function get_all_videos(): WP_REST_Response {
		$result = array();

		$query = new \WP_Query(
			array(
				'post_type'      => 'deep_pockets_videos',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'asc',
			)
		);

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
				'class' => 'video-listing-hero-banner',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$selectedvideos       = ! empty( $attributes['selectedVideoIDs'] ) ? json_decode( $attributes['selectedVideoIDs'] ) : array();
		$selectedvideos_array = ! empty( $selectedvideos ) ? wp_list_pluck( $selectedvideos, 'value' ) : array();

		$user_id        = get_current_user_id();
		$user_playlists = get_user_meta( $user_id, 'user_playlist', true );

		$number_of_posts = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 4;
		$display_arrows  = ! empty( $attributes['displayArrows'] ) ? $attributes['displayArrows'] : false;
		$display_dots    = ! empty( $attributes['displayDots'] ) ? $attributes['displayDots'] : false;

		$enable_overlay  = ! empty( $attributes['enableOverlay'] );
		$overlay_color   = ! empty( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : '#000000';
		$overlay_opacity = isset( $attributes['overlayOpacity'] ) ? (float) $attributes['overlayOpacity'] : 0;
		$overlay_opacity = max( 0, min( 1, $overlay_opacity ) );

		if ( ! $enable_overlay ) {
			$overlay_opacity = 0;
		}

		$video_selection_type = $attributes['videoSelectionToggle'];

		$query_args = array(
			'posts_per_page' => $number_of_posts,
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
		);

		if ( ! empty( $selectedvideos_array ) && ! $video_selection_type ) {
			$query_args['post__in'] = $selectedvideos_array;
			$query_args['orderby']  = 'post__in';
		} else {
			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = 'asc';
		}

		$query = new \WP_Query( $query_args );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<?php if ( $query->have_posts() ) { ?>
				<div class="video-listing-hero-banner__boxes"
					data-display-arrows="<?php echo esc_attr( $display_arrows ? 'true' : 'false' ); ?>"
					data-block-dots="<?php echo esc_attr( $display_dots ? 'true' : 'false' ); ?>"
				>
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$post_id            = get_the_ID();
						$video_banner_image = get_field( 'banner_image', get_the_ID() );
						$video_watch_time   = get_user_meta( $user_id, 'bc_watch_time', true );
						$main_video         = get_field( 'main_video', get_the_ID() );
						$main_video_source  = array(
							'url'           => '',
							'type'          => '',
							'attachment_id' => 0,
						);

						if ( ! empty( $main_video ) && is_array( $main_video ) ) {
							$main_video_source = $this->resolve_main_video_source( $main_video );
						}

						$video_key = $main_video_source['attachment_id'] > 0 ? (string) $main_video_source['attachment_id'] : '';
						$featured_img_url    = '';
						if ( $video_banner_image ) {
							$featured_img_url = $video_banner_image;
						} else {
							$placeholder_image = get_field( 'placeholder_image', 'option' );
							if ( ! empty( $placeholder_image ) ) {
								$featured_img_url = wp_get_attachment_image_url( $placeholder_image, 'full' );
							}
						}
						$style_sec_attr  = 'background: url(' . esc_url( $featured_img_url ) . ') center center/cover no-repeat;';
						$style_sec_attr .= '--overlay-color:' . esc_attr( $overlay_color ) . ';';
						$style_sec_attr .= '--overlay-opacity:' . esc_attr( $overlay_opacity ) . ';';
						?>
						<div class="video-listing-hero-banner__slide" style="<?php echo esc_attr( $style_sec_attr ); ?>">
							<div class="container">
								<div class="video-listing-hero-banner__slide-caption">
									<h1><?php echo esc_html( get_the_title() ); ?></h1>
									<p><?php echo esc_html( get_the_excerpt() ); ?></p>
									<div class="video-listing-hero-banner__btn-wrapper">
										<?php
										if ( $main_video_source['attachment_id'] > 0 ) {
											if ( ! empty( $video_watch_time ) && is_array( $video_watch_time ) && isset( $video_watch_time[ $video_key ] ) ) {
												?>
													<div
														class="btn-main play-btn play-from-beginning"
														data-post-id="<?php echo esc_attr( $post_id ); ?>"
														<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
															data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
															<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
																data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
															<?php endif; ?>
															data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
														<?php endif; ?>
													><span><?php esc_html_e( 'Play', 'vaultrix' ); ?></span></div>
													<div class="v-watch-now btn-transparent resume-now btn-main" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="play-icon"></span><?php esc_html_e( 'Continue Watch', 'vaultrix' ); ?></div>
												<?php
											} else {
												?>
													<div class="btn-main play-btn v-watch-now" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>"><span><?php esc_html_e( 'Play', 'vaultrix' ); ?></span></div>
												<?php
											}
										}
										?>
										<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $post_id ); ?>">
											<div class="tooltip-text" style="display:none"></div>
											<?php
											if ( $this->dp_check_post_id_in_playlist( $user_playlists, $post_id ) ) {
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
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<div class="video-listing-hero-banner__navigation">
					<div class="video-listing-hero-banner__pause-btn">
						<i class="fas fa-play-circle"></i>
						<i class="fas fa-pause-circle"></i>
					</div>
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
		<?php
		return ob_get_clean();
	}

	/**
	 * Check if the post id is in the playlist.
	 *
	 * @param array $array Array of playlists.
	 * @param int $value Value of the post id.
	 * @return bool True if the post id is in the playlist, false otherwise.
	 */
	public function dp_check_post_id_in_playlist( $array, $value ) {

		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $item ) {
				if ( is_array( $item ) && array_key_exists( 'video', $item ) && in_array( $value, $item['video'] ) ) { // phpcs:ignore
					return true;
				}
			}
		}

		return false;
	}
}
