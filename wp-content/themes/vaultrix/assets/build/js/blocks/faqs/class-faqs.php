<?php
/**
 * Registers the vaultrix/faqs block.
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
 *  Class for the vaultrix/faqs block.
 */
class Faqs extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'faqs';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'rest_api_init', array( $this, 'vaultrix_register_api_endpoint' ) );
	}

	/**
	 * Register custom api endpoints to fetch faqs data.
	 *
	 * @since 1.0.0
	 */
	public function vaultrix_register_api_endpoint() {

		register_rest_route(
			'dp_api',
			'/request/all_faqs',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dp_get_all_faqs_with_params' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get faqs posts with params.
	 */
	public function dp_get_all_faqs_with_params() {
		$args = array(
			'post_type'      => 'deep_pockets_faqs',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		return $this->fetchFaqs( $args );
	}

	/**
	 * List of faqs.
	 */
	public function fetchFaqs( $args ) {
		$result = array();

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {

			$i = 0;

			while ( $query->have_posts() ) {

				$query->the_post();

				$block_post_id = get_the_ID();
				$block_title   = get_the_title();

				$result[ $i ]['id']    = $block_post_id;
				$result[ $i ]['title'] = $block_title;

				$i++;
			}
		}

		return new \WP_REST_Response( $result, 200 );
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

		$classes = 'faq-listing';

		$alignment = ! empty( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : '';
		$classes  .= ' ' . $alignment;

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes,
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$selectedfaqs       = ! empty( $attributes['selectedFaqsIDs'] ) ? json_decode( $attributes['selectedFaqsIDs'] ) : array();
		$selectedfaqs_array = ! empty( $selectedfaqs ) ? wp_list_pluck( $selectedfaqs, 'value' ) : array();
		$heading            = ! empty( $attributes['heading'] ) ? $attributes['heading'] : '';
		$numberofpost       = ! empty( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 6;

		$heading_style  = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$question_style = ! empty( $attributes['questionColor'] ) ? 'style="color:' . $attributes['questionColor'] . '"' : '';
		$answer_style   = ! empty( $attributes['answerColor'] ) ? 'style="color:' . $attributes['answerColor'] . '"' : '';

		$query_args = array(
			'posts_per_page'      => $numberofpost,
			'post_type'           => 'deep_pockets_faqs',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
		);

		if ( ! empty( $selectedfaqs_array ) ) {
			$query_args['post__in'] = $selectedfaqs_array;
			$query_args['orderby']  = 'post__in';
		} else {
			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = 'asc';
		}

		$query = new \WP_Query( $query_args );

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>  >
			<div class="container">
				<?php
				if ( ! empty( $heading ) ) {
					?>
					<div class="faq-listing__top">
						<?php if ( ! empty( $heading ) && $attributes['showheading'] ) : ?>
							<div class="faq-listing__heading">
								<h2 <?php echo wp_kses_post( $heading_style ); ?>><?php echo esc_html( $heading ); ?></h2>
							</div>
						<?php endif; ?>
					</div>
					<?php
				}
				?>
				<?php if ( $query->have_posts() ) { ?>
					<div class="faq-listing__boxes">
						<?php
						$count = 0;
						while ( $query->have_posts() ) {
							$query->the_post();
							$small_description = get_the_content();
							?>
							<div class="faq-listing__item">
								<div class="faq-listing__item-header">
									<div class="faq-listing__item-heading" <?php echo wp_kses_post( $question_style ); ?>>
										<?php
										echo esc_html( get_the_title() );
										?>
									</div>
									<span class="faq-listing__item-icon"></span>
								</div>
								<div class="faq-listing__item-desc" <?php echo wp_kses_post( $answer_style ); ?>>
									<?php echo $small_description; //phpcs:ignore ?>
								</div>
							</div>
							<?php
							$count++;
						}
						wp_reset_postdata();
						?>
					</div>
					<?php
				} else {
					?>
					<div class="no-article-found-div">
						<p><?php esc_html_e( 'No faqs Found', 'vaultrix' ); ?></p>
					</div>
					<?php
				}
				if ( $attributes['showSeeAllLink'] && ! empty( $attributes['seeAllLink'] ) && ! empty( $attributes['seeAllLinkText'] ) ) {
					?>
					<div class="faq-listing__link">
						<a href="<?php echo esc_url( $attributes['seeAllLink'] ); ?>" class="btn-main btn-transparent"><?php echo esc_html( $attributes['seeAllLinkText'] ); ?></a>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}
