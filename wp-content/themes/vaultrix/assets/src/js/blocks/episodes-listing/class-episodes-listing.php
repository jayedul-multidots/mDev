<?php
/**
 * Registers the vaultrix/episodes-listing block.
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
 *  Class for the vaultrix/episodes-listing block.
 */
class Episodes_Listing extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'episodes-listing';
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

		$alignment = isset( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : 'center';
		$bgStyle   = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '';
		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'episodes-listing align-' . $alignment,
				'id'    => $attributes['blockID'],
				'style' => 'background-color:' . $bgStyle,
			)
		);

		// attributes.
		$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$highlighted_text  = isset( $attributes['highlightedText'] ) ? $attributes['highlightedText'] : '';
		$btn_text          = isset( $attributes['btnText'] ) ? $attributes['btnText'] : '';
		$btn_link          = isset( $attributes['btnLink'] ) ? $attributes['btnLink'] : '';
		$selected_category = isset( $attributes['selectedCategory'] ) ? $attributes['selectedCategory'] : '';
		$number_of_posts   = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 15;

		$selected_episodes       = ! empty( $attributes['selectedEpisodeIDs'] ) ? json_decode( $attributes['selectedEpisodeIDs'] ) : array();
		$selected_episodes_array = ! empty( $selected_episodes ) ? wp_list_pluck( $selected_episodes, 'value' ) : array();

		$heading_style = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';

		$current_user_id = get_current_user_id();
		$query_args      = array(
			'posts_per_page' => $number_of_posts,
			'post_type'      => 'deep_pockets_videos',
			'post_status'    => 'publish',
		);

		if ( ! empty( $selected_category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'video-show-category',
					'field'    => 'slug',
					'terms'    => $selected_category,
				),
			);
		}

		if ( ! empty( $selected_episodes_array ) ) {
			$query_args['post__in'] = $selected_episodes_array;
			$query_args['orderby']  = 'post__in';
		} else {
			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = 'asc';
		}

		$category = get_term_by( 'slug', $selected_category, 'video-show-category' );
		$query    = new \WP_Query( $query_args );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="container">
				<div class="episodes-listing__head">
					<?php if ( ! empty( $heading ) ) : ?>
						<h3 class="episodes-listing__heading" <?php echo wp_kses_post( $heading_style ); ?> ><?php echo esc_html( $heading ); ?><span class="highlighted-text"><?php echo esc_html( $highlighted_text ); ?></span></h3>
					<?php endif; ?>
				</div>
				<?php if ( $query->have_posts() ) { ?>
					<div class="episodes-listing__boxes">
						<?php
						while ( $query->have_posts() ) {
							$query->the_post();
							?>
							<div class="episodes-listing__box" data-post-id="<?php echo get_the_ID(); ?>">
								<?php if ( ( ! is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) || ( is_user_logged_in() && ! pmpro_hasMembershipLevel( null, $current_user_id ) ) ) { ?>
									<a href="/membership-account/membership-checkout/" class="episodes-listing__box-overlay" target="_blank"><span></span></a>
								<?php } ?>
								<div class="episodes-listing__box-content">
									<div class="episodes-listing__box-img">
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
									<div class="episodes-listing__series-detail">
										<?php
										if ( $category ) {
											$series_logo = get_field( 'add_series_logo', 'video-show-category_' . $category->term_id );
											if ( $series_logo ) {
												?>
													<div class="episodes-listing__logo">
														<img width="100" height="100" src="<?php echo esc_url( $series_logo ); ?>"  alt="<?php echo esc_attr( $category->name ); ?>" >
													</div>
												<?php
											}
											?>
											<div class="episodes-listing__name">
												<span class="episodes-listing__series-name"><?php echo esc_html( $category->name ); ?></span>
												<span class="episodes-listing__episode-name"><?php echo esc_html( get_the_title() ); ?></span>
											</div>
											<?php
										}
										?>
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
				<?php if ( $attributes['showButton'] && ! empty( $btn_link ) && ! empty( $btn_text ) ) : ?>
					<div class="episodes-listing__btn">
						<a class="btn-main" href="<?php echo esc_url( $btn_link ); ?>"><?php echo esc_html( $btn_text ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
