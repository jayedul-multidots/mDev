<?php
/**
 * Registers the vaultrix/series-card-slider block.
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
 *  Class for the vaultrix/series-card-slider block.
 */
class Series_Card_slider extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'series-card-slider';
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

		$classes = 'series-card-slider-section';

		$layout    = ! empty( $attributes['blockLayout'] ) ? $attributes['blockLayout'] : '';
		$alignment = ! empty( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : '';

		$classes .= ' ' . $layout . ' ' . $alignment;

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading               = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$heading_style         = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$description           = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$description_style     = ! empty( $attributes['descriptionColor'] ) ? 'style="color:' . $attributes['descriptionColor'] . '"' : '';
		$selectedvideos        = ! empty( $attributes['selectedVideoIDs'] ) ? json_decode( $attributes['selectedVideoIDs'] ) : array();
		$selectedvideos_array  = ! empty( $selectedvideos ) ? wp_list_pluck( $selectedvideos, 'value' ) : array();
		$selected_sub_category = ! empty( $attributes['selectedSubCategory'] ) ? $attributes['selectedSubCategory'] : '';
		$display_autoslide     = ! empty( $attributes['displayAutoslide'] ) ? $attributes['displayAutoslide'] : false;
		$display_arrows        = ! empty( $attributes['displayArrows'] ) ? $attributes['displayArrows'] : false;
		$current_user_id       = get_current_user_id();
		$btnText               = isset( $attributes['btnText'] ) ? $attributes['btnText'] : '';
		$number_of_posts       = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 15;

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> 
			data-block-auto-slide="<?php echo esc_attr( $display_autoslide ? 'true' : 'false' ); ?>"
			data-display-arrows="<?php echo esc_attr( $display_arrows ? 'true' : 'false' ); ?>"
		>
			<div class="container">
				<div class="series-card-slider-section__heading-section">
					<?php if ( ! empty( $heading ) && $attributes['showheading'] ) : ?>
						<h3 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo $heading; // phpcs:ignore ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $description ) && $attributes['showdesc'] ) : ?>
						<p <?php echo wp_kses_post( $description_style ); ?> ><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<div class="series-card-slider-section__video-series-wrapper">
					<?php
					$parent_category = get_term_by( 'slug', 'financial-masterclass', 'video-show-category' );

					// Check if the parent category term exists.
					if ( $parent_category ) {
						// Get the subcategory terms of the parent category.
						$subcategories = get_terms(
							array(
								'taxonomy' => 'video-show-category', // Use 'category' for default WordPress categories.
								'child_of' => $parent_category->term_id,
							)
						);

						$subcategories_array = ! empty( $attributes['selectedSeriesIDs'] ) ? json_decode( $attributes['selectedSeriesIDs'] ) : array();

						if ( empty( $subcategories_array ) ) {
							$counter = 0;
							foreach ( $subcategories as $subcategory ) {
								if ( $counter < $number_of_posts ) {
									$video_img_url   = get_field( 'add_feature_image', 'video-show-category_' . $subcategory->term_id );
									$seasons         = get_terms(
										array(
											'taxonomy'   => 'video-season-category',
											'hide_empty' => false, // Retrieve even if they have no posts
										)
									);
									$season_counter  = 0;
									$episode_counter = 0;
									foreach ( $seasons as $season ) {
										$season_posts = new \WP_Query(
											array(
												'post_type' => 'deep_pockets_videos',
												'tax_query' => array( // phpcs:ignore
													'relation' => 'AND', // Add an 'AND' relation between the conditions
													array(
														'taxonomy' => 'video-season-category',
														'field'    => 'term_id',
														'terms'    => $season->term_id,
													),
													array(
														'taxonomy' => 'video-show-category',
														'field'    => 'slug',
														'terms'    => 'financial-masterclass',
													),
													array(
														'taxonomy' => 'video-show-category',
														'field'    => 'slug',
														'terms'    => $subcategory->slug,
													),
												),
											)
										);
										if ( $season_posts->have_posts() ) {
											$season_counter++;
											while ( $season_posts->have_posts() ) {
												$season_posts->the_post();
												$episode_counter++;
											}
										}
									}
									?>
									<div class="series-card-slider-section__video-series">
										<?php
										if ( $layout === 'layout-1' ) {
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
											<div class="series-card-slider-section__video-series-overlay">
												<h3><?php echo esc_html( $subcategory->name ); ?></h3>
												<div class="season-details-wrapper">
													<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
													<span class="season-details dot-seprator"> • </span>
													<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
												</div>
												<?php if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) { ?>
													<a href="/membership-account/membership-checkout/" target="_blank"><p class="btn-main"><?php echo esc_html( $btnText ); ?></p></a>
												<?php } else { ?>
													<a href="#"><p class="btn-main"><?php echo esc_html( $btnText ); ?></p></a>
												<?php } ?>
													
											</div>
											
											<?php
										} else {
											if ( $video_img_url ) {
												if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) {
													?>
														<a href="/membership-account/membership-checkout/" target="_blank"><img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster"></img></a>
													<?php
												} else {
													?>
														<img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster"></img>
													<?php
												}
											} else {
												$placeholder_image = get_field( 'placeholder_image', 'option' );
												if ( ! empty( $placeholder_image ) ) {
													echo wp_get_attachment_image( $placeholder_image, 'full' );
												}
											}
											?>
											<div class="series-card-slider-section__video-series-overlay">
												<h3><?php echo esc_html( $subcategory->name ); ?></h3>
												<div class="season-details-wrapper">
													<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
													<span class="season-details dot-seprator"> • </span>
													<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
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
							foreach ( $subcategories_array as $subcategories_array_value ) {
								if ( $counter < $number_of_posts ) {
									$seriesterm      = get_term_by( 'slug', $subcategories_array_value->value, 'video-show-category' );
									$video_img_url   = get_field( 'add_feature_image', 'video-show-category_' . $seriesterm->term_id );
									$seasons         = get_terms(
										array(
											'taxonomy'   => 'video-season-category',
											'hide_empty' => false, // Retrieve even if they have no posts
										)
									);
									$season_counter  = 0;
									$episode_counter = 0;
									foreach ( $seasons as $season ) {
										$season_posts = new \WP_Query(
											array(
												'post_type' => 'deep_pockets_videos',
												'tax_query' => array( // phpcs:ignore
													'relation' => 'AND', // Add an 'AND' relation between the conditions
													array(
														'taxonomy' => 'video-season-category',
														'field'    => 'term_id',
														'terms'    => $season->term_id,
													),
													array(
														'taxonomy' => 'video-show-category',
														'field'    => 'slug',
														'terms'    => 'financial-masterclass',
													),
													array(
														'taxonomy' => 'video-show-category',
														'field'    => 'slug',
														'terms'    => $seriesterm->slug,
													),
												),
											)
										);
										if ( $season_posts->have_posts() ) {
											$season_counter++;
											while ( $season_posts->have_posts() ) {
												$season_posts->the_post();
												$episode_counter++;
											}
										}
									}
									?>
									<div class="series-card-slider-section__video-series">
										<?php
										if ( $layout === 'layout-1' ) {
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
											<div class="series-card-slider-section__video-series-overlay">
												<h3><?php echo esc_html( $seriesterm->name ); ?></h3>
												<div class="season-details-wrapper">
													<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
													<span class="season-details dot-seprator"> • </span>
													<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
												</div>
												<?php if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) { ?>
													<a href="/membership-account/membership-checkout/" target="_blank"><p class="btn-main"><?php echo esc_html( $btnText ); ?></p></a>
												<?php } else { ?>
													<a href="#"><p class="btn-main"><?php echo esc_html( $btnText ); ?></p></a>
												<?php } ?>
													
											</div>
											
											<?php
										} else {
											if ( $video_img_url ) {
												if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) {
													?>
														<a href="/membership-account/membership-checkout/" target="_blank"><img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster"></img></a>
													<?php
												} else {
													?>
														<img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster"></img>
													<?php
												}
											} else {
												$placeholder_image = get_field( 'placeholder_image', 'option' );
												if ( ! empty( $placeholder_image ) ) {
													echo wp_get_attachment_image( $placeholder_image, 'full' );
												}
											}
											?>
											<div class="series-card-slider-section__video-series-overlay">
												<h3><?php echo esc_html( $seriesterm->name ); ?></h3>
												<div class="season-details-wrapper">
													<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
													<span class="season-details dot-seprator"> • </span>
													<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
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
					}
					?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();

	}
}
