<?php
/**
 * Registers the vaultrix/recently-added-videos block.
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
 *  Class for the vaultrix/recently-added-videos block.
 */
class Recently_Added_Videos extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'recently-added-videos';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {}

	/**
	 * 2026-07-01: Resolves ACF main_video for watch-progress bar (replaces Brightcove ID lookup).
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
	 * 2026-07-01: Reads saved watch position keyed by main_video attachment ID.
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
		array $attributes,
		string $content,
		WP_Block $block
	): string {

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'recently-added-videos',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading               = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$bg_style              = ! empty( $attributes['bgColor'] ) ? 'background: ' . $attributes['bgColor'] : 'background: #001e4e';
		$heading_style         = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$selected_categories   = ! empty( $attributes['selectedCategory'] ) ? $attributes['selectedCategory'] : '';
		$selected_sub_category = ! empty( $attributes['selectedSubCategory'] ) ? $attributes['selectedSubCategory'] : '';
		$number_of_posts       = ! empty( $attributes['numberOfPosts'] ) ? (int) $attributes['numberOfPosts'] : 12; // 2026-07-01: Editor setting for paginated grid size.
		$current_user_id       = get_current_user_id();
		$paged                 = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args                  = array(
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
			'posts_per_page' => $number_of_posts,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'paged'          => $paged,
		);

		if ( ! empty( $selected_categories ) && empty( $selected_sub_category ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				),
			);
		} elseif ( ! empty( $selected_categories ) && ! empty( $selected_sub_category ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
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

		$query = new \WP_Query( $args );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="container">
				<div class="recently-added-videos__head">
					<h1 class="recently-added-videos__title" <?php echo wp_kses_post( $heading_style ); ?>><?php echo esc_html( $heading ); ?></h1>
				</div>
				<?php if ( $query->have_posts() ) { ?>
					<div class="recently-added-videos__wrapper">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						?>
							<div class="recently-added-videos__thumbnail">
								<div class="recently-added-videos__thumbnail-wrapper">
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
								// 2026-07-01: Progress bar uses main_video attachment ID (same pattern as video-listing-slider).
								$video_post_id     = get_the_ID();
								$main_video        = get_field( 'main_video', $video_post_id );
								$main_video_source = array(
									'url'           => '',
									'type'          => '',
									'attachment_id' => 0,
								);

								if ( ! empty( $main_video ) && is_array( $main_video ) ) {
									$main_video_source = $this->resolve_main_video_source( $main_video );
								}

								$watch_progress = $this->get_video_watch_progress(
									$main_video_source['attachment_id'],
									$current_user_id
								);
								$video_total_duration = $watch_progress['total_duration'];
								$video_watch_duration = $watch_progress['watch_duration'];

								if ( '' !== $video_total_duration && '' !== $video_watch_duration ) {
									$percentage_watched = ( $video_watch_duration / $video_total_duration ) * 100;
									?>
										<div class="video-listing-slider__duration-bar video-progress__bar">
											<div class="video-listing-slider__duration-bar-inner video-progress__bar-inner" style="width:<?php echo esc_attr( $percentage_watched ); ?>%"></div>
										</div>
									<?php
								}
								?>
								<div class="post-content">
									<div class="post-title"><?php echo esc_html( get_the_title() ); ?></div>
									<div class="watch-now-btn btn-main video-modal" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">Watch now !</div>
								</div>
							</div>
						<?php
					}
					wp_reset_postdata();
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
				if ( $query->max_num_pages > 1 ) :
					?>
						<div class="recently-added-videos__pagination">
							<?php
								echo paginate_links( // phpcs:ignore
									array(
										'total'     => $query->max_num_pages,
										'current'   => max( 1, get_query_var( 'paged' ) ),
										'prev_text' => 'Previous',
										'next_text' => 'Next',
									)
								);
							?>
						</div>
					<?php
				endif;
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
