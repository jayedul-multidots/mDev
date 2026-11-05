<?php
/**
 * Registers the vaultrix/pricing-plan block.
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
 *  Class for the vaultrix/pricing-plan block.
 */
class Pricing_Plan extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'pricing-plan';
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

		$classes = 'pricing-plan-block';

		$alignment = ! empty( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : '';

		$classes .= ' ' . $alignment;

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
			)
		);

		// attributes.
		$heading              = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$description          = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$buttonText           = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
		$bottomThumbnailImage = isset( $attributes['bottomThumbnailImage'] ) ? $attributes['bottomThumbnailImage'] : '';

		$heading_style = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$desc_style    = ! empty( $attributes['descriptionColor'] ) ? 'style="color:' . $attributes['descriptionColor'] . '"' : '';

		$style_attr = '';
		$style_attr = 'background-color: ' . $attributes['bgColor'];

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="popup" style="<?php echo esc_attr( $style_attr ); ?>">
				<!-- Pricing Popup -->
				<div class="pricing-popup">
					<div class="popup__wrapper container">
						<div class="popup__head">
							<?php if ( ! empty( $heading ) && $attributes['showheading'] ) : ?>
								<h2 <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( $description ) && $attributes['showdesc'] ) : ?>
								<p <?php echo wp_kses_post( $desc_style ); ?> ><?php echo esc_html( $description ); ?></p>
							<?php endif; ?>
						</div>	
						<?php
							$levels     = pmpro_getAllLevels();
							$counter    = 0;
							$level_type = '';
						foreach ( $levels as $level ) {
							$counter++;
						}
						echo '<div class="popup-content col-' . esc_html( $counter ) . '">';
						foreach ( $levels as $level ) {
							if ( $level->cycle_period === 'Year' && $level->cycle_number == 1 ) {
								$level_type = 'Annually';
							} elseif ( $level->cycle_period === 'Month' && $level->cycle_number == 1 ) {
								$level_type = 'Monthly';
							}
							$short_description = ! empty( get_option( 'pmpro_custom_field_' . $level->id ) ) ? get_option( 'pmpro_custom_field_' . $level->id ) : '';
							$checkout_page_id  = get_option( 'pmpro_checkout_page_id' );
							$checkout_url      = add_query_arg( 'level', $level->id, get_the_permalink( $checkout_page_id ) );

							echo '<div class="membership-card">';
								echo '<div class="membership-head">';
									echo '<h2 class="heading">' . esc_html( $level->name ) . '</h2>';
							if ( ! empty( $short_description ) ) {
								echo '<p class="head-description">' . esc_html( $short_description ) . '</p>';
							}
								echo '</div>';

								echo '<div class="membership-content">';
									echo '<div class="price"><sup>$ </sup><span>' . esc_html( $level->initial_payment ) . '</span> <sub>/ ' . esc_html( $level_type ) . '</sub></div>';
									echo '<div class="description">' . wp_kses_post( $level->description ) . '</div>';
									echo '<div class="btn-main"><a href="' . esc_url( $checkout_url ) . '"><i class="fas fa-play"></i>' . esc_html( $buttonText ) . '</a></div>';
							if ( ! empty( $bottomThumbnailImage['id'] ) ) {
								echo '<div class="bottom-thumbnail">';
									echo wp_get_attachment_image( $bottomThumbnailImage['id'], 'full' );
								echo '</div>';
							}
								echo '</div>';

							echo '</div>';
							// Add any additional HTML or styling as needed
							$counter++;
						}
						echo '</div>';
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
