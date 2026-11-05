<?php
/**
 * Registers the vaultrix/show-card-slider block.
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
 *  Class for the vaultrix/show-card-slider block.
 */
class Show_Card_slider extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'show-card-slider';
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

		$classes = 'show-card-slider-section';

		$layout    = ! empty( $attributes['blockLayout'] ) ? $attributes['blockLayout'] : '';
		$alignment = ! empty( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : '';

		$classes .= ' ' . $alignment;

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$heading_style     = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$description_style = ! empty( $attributes['descriptionColor'] ) ? 'style="color:' . $attributes['descriptionColor'] . '"' : '';
		$display_autoslide = ! empty( $attributes['displayAutoSlide'] ) ? $attributes['displayAutoSlide'] : false;
		$display_arrows    = ! empty( $attributes['displayArrows'] ) ? $attributes['displayArrows'] : false;
		$current_user_id   = get_current_user_id();
		$btnText           = isset( $attributes['btnText'] ) ? $attributes['btnText'] : '';
		$number_of_posts   = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 15;
		$bg_color          = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#001e4e';
		$style_sec_attr    = 'background: ' . $bg_color . ';';
		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> 
			data-block-auto-slide="<?php echo esc_attr( $display_autoslide ? 'true' : 'false' ); ?>"
			data-display-arrows="<?php echo esc_attr( $display_arrows ? 'true' : 'false' ); ?>"
			style="<?php echo esc_attr( $style_sec_attr ); ?>"
		>
			<div class="container">
				<div class="show-card-slider-section__heading-section">
					<?php if ( ! empty( $heading ) && $attributes['showHeading'] ) : ?>
						<h2 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo $heading; // phpcs:ignore ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $description ) && $attributes['showDesc'] ) : ?>
						<p <?php echo wp_kses_post( $description_style ); ?> ><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<div class="show-card-slider-section__video-show-wrapper">
					<?php

					$selected_shows = ! empty( $attributes['selectedShowsIDs'] ) ? json_decode( $attributes['selectedShowsIDs'] ) : array();

					if ( empty( $selected_shows ) ) {
						$counter      = 0;
						$all_shows    = array();
						$parent_shows = get_terms(
							array(
								'taxonomy'   => 'video-show-category',
								'hide_empty' => false,
								'parent'     => 0,
							)
						);
						foreach ( $parent_shows as $show ) {
							$all_shows[]      = array(
								'name' => $show->name,
								'slug' => $show->slug,
							);
							$child_categories = get_terms(
								array(
									'taxonomy'   => 'video-show-category',
									'hide_empty' => false,
									'parent'     => $show->term_id,
								)
							);
							foreach ( $child_categories as $child_category ) {
								$all_shows[] = array(
									'name' => $child_category->name,
									'slug' => $child_category->slug,
								);
							}
						}

						foreach ( $all_shows as $show ) {
							if ( $counter < $number_of_posts ) {
								$show_term     = get_term_by( 'slug', $show['slug'], 'video-show-category' );
								$video_img_url = get_field( 'add_feature_image', 'video-show-category_' . $show_term->term_id );
								?>
									<div class="show-card-slider-section__video-show">
										<?php
										if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) {
											?>
												<a href="/membership-account/membership-checkout/" target="_blank">
													<?php
													if ( $video_img_url ) {
														?>
															<img src="<?php echo esc_url( $video_img_url ); ?>" alt="show poster"></img>
														<?php
													} else {
														$placeholder_image = get_field( 'placeholder_image', 'option' );
														if ( ! empty( $placeholder_image ) ) {
															echo wp_get_attachment_image( $placeholder_image, 'full' );
														}
													}
													?>
													<div class="show-card-slider-section__video-show-overlay">
														<div class="show-card-slider-section__video-show-detail">
															<h3><?php echo esc_html( $show_term->name ); ?></h3>
															<p class="show-details"><?php echo esc_html( $show_term->count ); ?><?php echo esc_attr( ' Episodes', 'vaultrix' ); ?></p>
														</div>
													</div>
												</a>
											<?php
										} else {
											if ( $video_img_url ) {
												?>
												<img src="<?php echo esc_url( $video_img_url ); ?>" alt="show poster"></img>
												<?php
											} else {
												$placeholder_image = get_field( 'placeholder_image', 'option' );
												if ( ! empty( $placeholder_image ) ) {
													echo wp_get_attachment_image( $placeholder_image, 'full' );
												}
											}
											?>
											<div class="show-card-slider-section__video-show-overlay">
												<div class="show-card-slider-section__video-show-detail">
													<h3><?php echo esc_html( $show_term->name ); ?></h3>
													<p class="show-details"><?php echo esc_html( $show_term->count ); ?><?php echo esc_attr( ' Episodes', 'vaultrix' ); ?></p>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								<?php
								$counter++;
							}
						}
					} else {
						$counter = 0;
						foreach ( $selected_shows as $show ) {
							if ( $counter < $number_of_posts ) {
								$show_term     = get_term_by( 'slug', $show->value, 'video-show-category' );
								$video_img_url = get_field( 'add_feature_image', 'video-show-category_' . $show_term->term_id );
								$seasons       = get_terms(
									array(
										'taxonomy'   => 'video-season-category',
										'hide_empty' => false, // Retrieve even if they have no posts
									)
								);
								?>
									<div class="show-card-slider-section__video-show">
									<?php
									if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) {
										?>
											<a href="/membership-account/membership-checkout/" target="_blank">
												<?php
												if ( $video_img_url ) {
													?>
														<img src="<?php echo esc_url( $video_img_url ); ?>" alt="show poster"></img>
													<?php
												} else {
													$placeholder_image = get_field( 'placeholder_image', 'option' );
													if ( ! empty( $placeholder_image ) ) {
														echo wp_get_attachment_image( $placeholder_image, 'full' );
													}
												}
												?>
												<div class="show-card-slider-section__video-show-overlay">
													<div class="show-card-slider-section__video-show-detail">
														<h3><?php echo esc_html( $show_term->name ); ?></h3>
														<p class="show-details"><?php echo esc_html( $show_term->count ); ?><?php echo esc_attr( ' Episodes', 'vaultrix' ); ?></p>
													</div>
												</div>
											</a>
										<?php
									} else {
										if ( $video_img_url ) {
											?>
												<img src="<?php echo esc_url( $video_img_url ); ?>" alt="show poster"></img>
											<?php
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
										?>
										<div class="show-card-slider-section__video-show-overlay">
											<div class="show-card-slider-section__video-show-detail">
												<h3><?php echo esc_html( $show_term->name ); ?></h3>
												<p class="show-details"><?php echo esc_html( $show_term->count ); ?><?php echo esc_attr( ' Episodes', 'vaultrix' ); ?></p>
											</div>
										</div>
										<?php
									}
									?>
									</div>
								<?php
								$counter++;
							}
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();

	}
}
