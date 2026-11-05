<?php
/**
 * Registers the vaultrix/most-viewed block.
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
 *  Class for the vaultrix/most-viewed block.
 */
class Most_Viewed extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'most-viewed';
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
				'class' => 'most-viewed-section',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$seeAllLinkText = isset( $attributes['seeAllLinkText'] ) ? $attributes['seeAllLinkText'] : '';
		$seeAllLink     = isset( $attributes['seeAllLink'] ) ? $attributes['seeAllLink'] : '';

		$heading_style = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="container">
				<div class="most-viewed-section__heading-section">
					<?php if ( ! empty( $heading ) ) : ?>
						<h3 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
					<?php if ( $attributes['showSeeAllLink'] && ! empty( $seeAllLink ) && ! empty( $seeAllLinkText ) ) : ?>
						<a href="<?php echo esc_url( $seeAllLink ); ?>" ><p><?php echo esc_html( $seeAllLinkText ); ?></p></a>
						<?php
					endif;
					?>
				</div>
				<div class="most-viewed-section__video-series-wrapper">
					<?php
					$popular_videos = get_trending_articles();
					$counter        = 0;
					$child_terms    = array();

					foreach ( $popular_videos as $video ) {
						$terms = get_the_terms( $video->ID, 'video-show-category' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								if ( $term->parent != 0 ) {
									if ( ! in_array( $term, $child_terms ) ) {  // phpcs:ignore
										$child_terms[] = $term;
									}
								}
							}
						}
					}
					if ( ! empty( $child_terms ) ) {
						foreach ( $child_terms as $term ) {
							$parent_term = ! empty( $term->parent ) && ! empty( get_term( $term->parent ) ) ? get_term( $term->parent ) : '';
							if ( $parent_term->slug !== 'financial-masterclass' ) {
								continue;
							}
							$video_img_url = get_field( 'add_feature_image', 'video-show-category_' . $term->term_id );
							$counter++;
							if ( $counter > 3 ) {
								break;
							}
							?>
							<div class="most-viewed-section__video-series">
								<div class="most-viewed-section__video-series-inner-wrapper series-modal" id="series-modal" data-series-id="<?php echo esc_attr( $term->term_id ); ?>">
									<?php
									if ( $video_img_url ) {
										?>
										<img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster" ></img>
										<?php
									} else {
										$placeholder_image = get_field( 'placeholder_image', 'option' );
										if ( ! empty( $placeholder_image ) ) {
											echo wp_get_attachment_image( $placeholder_image, 'full' );
										}
									}
									?>
									<div class="most-viewed-section__video-series-caption">
										<h3><?php echo esc_html( $term->name ); ?></h3>
										<?php
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
													'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
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
															'terms'    => $term->slug,
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
										<div class="season-details-wrapper">
											<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
											<span class="season-details dot-seprator"> • </span>
											<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
										</div>
									</div>
								</div>
								<p class="most-viewed-section__card-counter"><?php echo esc_html( $counter ); ?></p>
							</div>
							<?php
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
