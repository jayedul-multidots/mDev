<?php
/**
 * Registers the vaultrix/video-category-hero-banner block.
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
 *  Class for the vaultrix/video-category-hero-banner block.
 */
class Video_Category_Hero_Banner extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'video-category-hero-banner';
	}

	/**
	 * Resolve the trailer video source URL and mime type from an ACF file field.
	 *
	 * @param array $trailer_video ACF file field array.
	 * @return array{url: string, type: string}
	 */
	private function resolve_trailer_video_source( array $trailer_video ): array {
		$video_url     = $trailer_video['url'] ?? '';
		$video_type    = $trailer_video['mime_type'] ?? '';
		$attachment_id = $trailer_video['ID'] ?? ( $trailer_video['id'] ?? 0 );
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
			'url'  => $video_url,
			'type' => $video_type,
		);
	}

	/**
	 * Render block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 * @return string
	 */
	public function render_callback(
		// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		array $attributes,
		string $content,
		WP_Block $block
		// phpcs:enable
	): string {
		$block_id            = isset( $attributes['blockID'] ) ? $attributes['blockID'] : '';
		$bg_image            = isset( $attributes['bgImage'] ) ? $attributes['bgImage'] : array();
		$show_pre_heading    = ! empty( $attributes['showPreHeading'] );
		$show_heading        = ! empty( $attributes['showHeading'] );
		$show_desc           = ! empty( $attributes['showDesc'] );
		$show_button         = ! empty( $attributes['showButton'] );
		$pre_heading         = isset( $attributes['preHeading'] ) ? $attributes['preHeading'] : '';
		$heading             = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$description         = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$button              = isset( $attributes['button'] ) ? $attributes['button'] : '';
		$selected_video_id   = ! empty( $attributes['selectedVideoID'] ) ? (int) $attributes['selectedVideoID'] : 0;
		$bg_color            = isset( $attributes['bgColor'] ) ? $attributes['bgColor'] : '';
		$pre_heading_color   = isset( $attributes['preHeadingColor'] ) ? $attributes['preHeadingColor'] : '';
		$heading_color       = isset( $attributes['headingColor'] ) ? $attributes['headingColor'] : '';
		$description_color   = isset( $attributes['descriptionColor'] ) ? $attributes['descriptionColor'] : '';
		$content_alignment   = isset( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : 'left';

		$trailer_video = array();
		if ( $selected_video_id > 0 ) {
			$trailer_video = get_field( 'trailer_video', $selected_video_id );
		}

		$trailer_source = array();
		if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
			$trailer_source = $this->resolve_trailer_video_source( $trailer_video );
		}

		$block_styles = array();
		if ( ! empty( $bg_color ) ) {
			$block_styles[] = 'background-color:' . $bg_color;
		}
		if ( ! empty( $bg_image['url'] ) ) {
			$block_styles[] = 'background-image:url(' . $bg_image['url'] . ')';
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'video-category-hero-banner ' . esc_attr( $content_alignment ),
				'id'    => $block_id,
				'style' => ! empty( $block_styles ) ? implode( ';', $block_styles ) : '',
			)
		);

		$pre_heading_style = ! empty( $pre_heading_color ) ? 'style="color:' . esc_attr( $pre_heading_color ) . '"' : '';
		$heading_style     = ! empty( $heading_color ) ? 'style="color:' . esc_attr( $heading_color ) . '"' : '';
		$desc_style        = ! empty( $description_color ) ? 'style="color:' . esc_attr( $description_color ) . '"' : '';

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="container">
				<div class="video-category-hero-banner__wrapper">
					<?php if ( $pre_heading && $show_pre_heading ) : ?>
						<p class="video-category-hero-banner__preheading" <?php echo wp_kses_post( $pre_heading_style ); ?>><?php echo wp_kses_post( $pre_heading ); ?></p>
					<?php endif; ?>
					<?php if ( $heading && $show_heading ) : ?>
						<h1 class="video-category-hero-banner__heading" <?php echo wp_kses_post( $heading_style ); ?>><?php echo wp_kses_post( $heading ); ?></h1>
					<?php endif; ?>
					<?php if ( $description && $show_desc ) : ?>
						<p class="video-category-hero-banner__description" <?php echo wp_kses_post( $desc_style ); ?>><?php echo wp_kses_post( $description ); ?></p>
					<?php endif; ?>
					<?php if ( $show_button && $button && ! empty( $trailer_source['url'] ) ) : ?>
						<div
							class="btn-main v-watch-trailer"
							data-trailer-url="<?php echo esc_url( $trailer_source['url'] ); ?>"
							<?php echo ! empty( $trailer_source['type'] ) ? 'data-trailer-type="' . esc_attr( $trailer_source['type'] ) . '"' : ''; ?>
						>
							<span class="play-icon"></span>
							<span><?php echo wp_kses_post( $button ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
