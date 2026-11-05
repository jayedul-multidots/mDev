<?php
/**
 * Registers the vaultrix/video-gallery block.
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
 *  Class for the vaultrix/video-gallery block.
 */
class Video_Gallery extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'video-gallery';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'wp_ajax_video_main_item', array( $this, 'dp_video_main_item_callback' ) );
		add_action( 'wp_ajax_nopriv_video_main_item', array( $this, 'dp_video_main_item_callback' ) );
	}

	/**
	 * Return posts according to filters.
	 *
	 * @since 1.0.
	 */
	public function dp_video_main_item_callback() {

		$post_id = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_STRING );

		if ( ! empty( $post_id ) ) {
			ob_start();
			?>
				<div class="video-gallery_main__item_video__wrapper">
					<div class="video-gallery_main__item_video">
						<?php
						if ( has_post_thumbnail( $post_id ) ) {
							echo get_the_post_thumbnail( $post_id, 'full' );
						}
						?>
						<a href="<?php echo esc_url( get_site_url() . '/membership-account/membership-checkout/' ); ?>" class="video-gallery_main__item-icon"><i class="fas fa-play"></i></a>
					</div>
				</div>
				<div class="video-gallery_main__item_content-wrap">
					<h3 class="video-gallery_main__item-heading">
						<?php echo esc_html( get_the_title( $post_id ) ); ?>
					</h3>
					<p class="video-gallery_main__item-description">
						<?php echo esc_html( get_the_excerpt( $post_id ) ); ?>
					</p>
				</div>
			<?php
		}

		$html = ob_get_contents();
		ob_end_clean();

		$final_result = array();

		$final_result['ajax_response'] = $html;

		echo wp_json_encode( $final_result );

		wp_die();
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
				'class' => 'video-gallery',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading              = ! empty( $attributes['heading'] ) ? $attributes['heading'] : '';
		$description          = ! empty( $attributes['description'] ) ? $attributes['description'] : '';
		$categoryTitle        = ! empty( $attributes['categoryTitle'] ) ? $attributes['categoryTitle'] : '';
		$categoryPreviewTitle = ! empty( $attributes['categoryPreviewTitle'] ) ? $attributes['categoryPreviewTitle'] : '';

		$block_style_array = array();
		if ( ! empty( $attributes['bgColor'] ) ) {
			$block_style_array[] = 'background-color:' . $attributes['bgColor'] . '';
		}

		if ( ! empty( $attributes['bgImage']['url'] ) && empty( $attributes['bgGradientColor'] ) ) {
			$block_style_array[] = 'background-image: url(' . $attributes['bgImage']['url'] . ')';
		}

		$blockstyle = '';
		if ( ! empty( $block_style_array ) ) {
			$blockstyle = 'style="' . join( ';', $block_style_array ) . '"';
		}

		$heading_style     = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$description_style = ! empty( $attributes['descriptionColor'] ) ? 'style="color:' . $attributes['descriptionColor'] . '"' : '';

		$video_categories = get_terms(
			array(
				'taxonomy'   => 'video-show-category',
				'hide_empty' => false,
				'parent'     => 0,
			)
		);

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> <?php echo wp_kses_post( $blockstyle ); ?> >
			<div class="container">
				<?php
				if ( ! empty( $heading ) || ! empty( $description ) ) {
					?>
					<div class="video-gallery__top">
						<?php
						if ( ! empty( $heading ) ) {
							?>
							<h2 class="video-gallery__heading" <?php echo wp_kses_post( $heading_style ); ?>><?php echo esc_html( $heading ); ?></h2>
							<?php
						}
						if ( ! empty( $description ) ) {
							?>
							<p class="video-gallery__desc" <?php echo wp_kses_post( $description_style ); ?>><?php echo esc_html( $description ); ?></p>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
				<div class="video__gallery-wrapper">
				<?php
				if ( ! empty( $video_categories ) ) :
					?>
					<div class="video__gallery-tabs-main">
						<?php
						if ( ! empty( $categoryTitle ) ) {
							?>
							<div class="video-gallery__cat-title">
								<p><?php echo esc_html( $categoryTitle ); ?></p>
							</div>
							<?php
						}
						?>
						<ul class="video__gallery-tabs">
							<?php
							$category_count = 0;
							foreach ( $video_categories as $index => $video_category ) {
								$item_id               = $attributes['blockID'] . '-' . $index;
								$category_active_class = ( 0 === $category_count ) ? ' active' : '';
								?>
									<li class="video__gallery-tab<?php echo esc_attr( $category_active_class ); ?>">
										<a href="#<?php echo esc_attr( $item_id ); ?>" class="video__gallery-tab-item">
											<?php echo esc_html( $video_category->name ); ?>
										</a>
									</li>
								<?php
								$category_count++;
							}
							?>
						</ul>
					</div>
					<?php
				endif;
				?>
				<div class="video__gallery-content-wrapper">
					<?php if ( ! empty( $categoryPreviewTitle ) ) { ?>
						<div class="video-gallery__cat-preview-title">
							<p><?php echo esc_html( $categoryPreviewTitle ); ?></p>
						</div>
						<?php
					}
					if ( ! empty( $video_categories ) ) {
						?>
						<div class="video__gallery-content">
							<?php
							$count = 0;
							foreach ( $video_categories as $index => $video_category ) {
								$query_args = array(
									'posts_per_page'      => 5,
									'post_type'           => 'deep_pockets_videos',
									'post_status'         => 'publish',
									'ignore_sticky_posts' => 1,
								);

								if ( ! empty( $video_category->slug ) ) {
									$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
										array(
											'taxonomy' => 'video-show-category',
											'field'    => 'slug',
											'terms'    => $video_category->slug,
										),
									);
								}

								$video_query   = new \WP_Query( $query_args );
								$video_query_1 = new \WP_Query( $query_args );
								$item_id       = $attributes['blockID'] . '-' . $index;
								$active_class  = ( 0 === $count ) ? ' active' : '';
								?>
								<div class="video__gallery-tab-content<?php echo esc_attr( $active_class ); ?>" id="<?php echo esc_attr( $item_id ); ?>">
									<div class="video__gallery-tab-content-wrap">
										<?php
										if ( $video_query_1->have_posts() ) {
											?>
												<div class="video__gallery-tab-left-content">
													<?php
													$video_count = 0;
													while ( $video_query->have_posts() ) {
														$video_query->the_post();
														if ( 1 === $video_count ) {
															break;
														}

														?>
														<div class="video-gallery_main__item">
															<div class="video-gallery_main__item_video__wrapper">
																<div class="video-gallery_main__item_video">
																	<?php
																	if ( has_post_thumbnail() ) {
																		echo get_the_post_thumbnail( get_the_ID(), 'full' );
																	}
																	?>
																	<a href="<?php echo esc_url( get_site_url() . '/membership-account/membership-checkout/' ); ?>" class="video-gallery_main__item-icon"><i class="fas fa-play"></i></a>
																</div>
															</div>
															<div class="video-gallery_main__item_content-wrap">
																<h3 class="video-gallery_main__item-heading">
																	<?php echo esc_html( get_the_title() ); ?>
																</h3>
																<p class="video-gallery_main__item-description">
																	<?php echo esc_html( get_the_excerpt() ); ?>
																</p>
															</div>
														</div>
														<?php
														$video_count++;
													}
													wp_reset_postdata();
													?>
												</div>
												<div class="video__gallery-tab-right-content">
													<div class="video-gallery__item__wrapper">
														<?php
														while ( $video_query_1->have_posts() ) {
															$video_query_1->the_post();
															?>
															<div class="video-gallery__item" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
																<div class="video-gallery__item-content">
																	<div class="video-gallery__item-heading">
																		<?php echo esc_html( get_the_title() ); ?>
																		<span class="arrow"><i class="fas fa-play"></i></span>
																	</div>
																	<div class="video-gallery__item-description">
																		<?php echo esc_html( get_the_excerpt() ); ?>
																	</div>
																	<?php
																	if ( get_field( 'video_duration', get_the_ID() ) ) {
																		?>
																			<div class="video-gallery__item-duration">
																			<?php echo esc_html( get_field( 'video_duration', get_the_ID() ) ); ?>
																			</div>
																			<?php
																	}
																	?>
																</div>
															</div>
															<?php
														}
														wp_reset_postdata();
														?>
													</div>
												</div>
											<?php
										} else {
											?>
											<div class="no-article-found-div">
												<p><?php esc_html_e( 'No videos Found', 'vaultrix' ); ?></p>
											</div>
											<?php
										}
										?>
									</div>
								</div>
								<?php
								$count++;
							}
							?>
						</div>
						<?php
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}
