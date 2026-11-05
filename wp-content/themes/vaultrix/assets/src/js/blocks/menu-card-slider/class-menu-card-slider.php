<?php
/**
 * Registers the vaultrix/menu-card-slider block.
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
 *  Class for the vaultrix/text-and-video-with-slider block.
 */
class Menu_Card_Slider extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'menu-card-slider';
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

		$classes = 'mega-menu-card-slider';

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$display_autoslide = ! empty( $attributes['autoplay'] ) ? $attributes['autoplay'] : false;
		$display_arrows    = ! empty( $attributes['arrows'] ) ? $attributes['arrows'] : false;
		$display_dots      = ! empty( $attributes['dots'] ) ? $attributes['dots'] : false;
		$display_loopSlide          = ! empty( $attributes['loopSlide'] ) ? $attributes['loopSlide'] : false;
		$selected_parent_category   = ! empty( $attributes['selectedParentCategory'] ) ? $attributes['selectedParentCategory'] : 'series';
		$current_user_id            = get_current_user_id();
		$card_title_style           = ! empty( $attributes['cardTitleColor'] ) ? 'style="color:' . $attributes['cardTitleColor'] . '"' : '';

		ob_start();
		$current_user_id = get_current_user_id();

		if ( pmpro_hasMembershipLevel( null, $current_user_id ) ) {
			?>
			<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
				<div 
					class="mega-menu-card-slider-main slick-slider"
					data-block-auto-slide="<?php echo esc_attr( $display_autoslide ? 'true' : 'false' ); ?>"
					data-display-arrows="<?php echo esc_attr( $display_arrows ? 'true' : 'false' ); ?>"
					data-block-dots="<?php echo esc_attr( $display_dots ? 'true' : 'false' ); ?>"
					data-loop-slide="<?php echo esc_attr( $display_loopSlide ? 'true' : 'false' ); ?>"
				>
					<?php
					$parent_category = get_term_by( 'slug', $selected_parent_category, 'video-show-category' );

					// Check if the parent category term exists.
					if ( $parent_category ) {
						// Get the subcategory terms of the parent category.
						$subcategories = get_terms(
							array(
								'taxonomy' => 'video-show-category', // Use 'category' for default WordPress categories.
								'child_of' => $parent_category->term_id,
							)
						);
						if ( ! empty( $subcategories ) ) {
							foreach ( $subcategories as $subcategory ) {
								$video_img_url = get_field( 'add_feature_image', 'video-show-category_' . $subcategory->term_id );
								$series_link   = get_term_link( $subcategory->term_id );
								if ( ! is_wp_error( $series_link ) && filter_var( $series_link, FILTER_VALIDATE_URL ) ) {
									?>
									<a href="<?php echo esc_url( get_term_link( $subcategory->term_id ) ); // phpcs:ignore ?>">
									<?php
								} else {
									?>
									<a href="#">
									<?php
								}
								?>
									<div class="mega-menu-card-slider__item">
										<div class="mega-menu-card-slider__content">
											<div class="mega-menu-card-slider__card-image">
												<?php
												if ( $video_img_url ) {
													?>
															<img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster"></img>
														<?php
												} else {
													$placeholder_image = get_field( 'placeholder_image', 'option' );
													if ( ! empty( $placeholder_image ) ) {
														echo wp_get_attachment_image( $placeholder_image, 'full' );
													}
												}
												?>
											</div>
											<div class="mega-menu-card-slider__card-title" <?php echo wp_kses_post( $card_title_style ); ?>>
												<?php echo esc_html( $subcategory->name ); ?>
											</div>
											<p class="mega-menu-card-slider__card-description">All seasons available</p>
										</div>
									</div>
								</a>
								<?php
							}
						}
					}
					?>
				</div>
			</div>
			<?php
		} else {
			$pmpro_restriction_non_member_user_text = get_field( 'pmpro_restriction_non_member_user_text', 'option' );
			?>
				<div class="pmpro_content_message">
					<div class='dp-restricted-section-message'>
						<div class='container'>
							<h1 class='member-warning-message'> <?php echo esc_html( $pmpro_restriction_non_member_user_text ); ?></h1>
							<h2 class='member-warning-message'><span>Click here</span> to get your membership now.</h2>
						</div>
					</div>
				</div>
			<?php
		}
		return ob_get_clean();

	}
}
