<?php
/**
 * Registers the vaultrix/video-listing-filter block.
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
 *  Class for the vaultrix/video-listing-filter block.
 */
class Video_Listing_Filter extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'video-listing-filter';
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
				'class' => 'video-listing-filter',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$bg_style = ! empty( $attributes['bgColor'] ) ? 'background: ' . $attributes['bgColor'] : '';

		$video_show_categories = get_terms(
			array(
				'taxonomy'   => 'video-show-category',
				'hide_empty' => false,
				'parent'     => 0,
			)
		);

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="container">
				<div class="video-listing-filter__category-tab-wrapper">
					<?php
					foreach ( $video_show_categories as $video_show_category ) {
						?>
						<div class="video-listing-filter__category-tab btn-main btn-transparent">
							<p class="video-listing-filter__category-tab-inner" data-cat-slug="<?php echo esc_attr( $video_show_category->slug ); ?>">
								<a href="<?php echo esc_url( get_term_link( $video_show_category->term_id ) ); ?>"><?php echo esc_html( $video_show_category->name ); ?></a>
							</p>
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
