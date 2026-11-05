<?php
/**
 * Registers the vaultrix/video block.
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
 *  Class for the vaultrix/video block.
 */
class Video extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'video';
	}

	/**
	 * Resolve the main video source URL and mime type from an ACF file field.
	 *
	 * @param array $main_video ACF file field array.
	 * @return array{url: string, type: string}
	 */
	private function resolve_main_video_source( array $main_video ): array {
		$video_url       = $main_video['url'] ?? '';
		$video_type      = $main_video['mime_type'] ?? '';
		$attachment_id   = $main_video['ID'] ?? ( $main_video['id'] ?? 0 );
		$attachment_id   = (int) $attachment_id;

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
	 * Render the video player markup.
	 *
	 * @param array $main_video ACF file field array.
	 * @return string
	 */
	private function render_video_player( array $main_video ): string {
		$video_source = $this->resolve_main_video_source( $main_video );

		if ( empty( $video_source['url'] ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="dp-video__wrapper">
			<div class="pause-button"><i class="fas fa-play"></i></div>
			<video controls playsinline preload="metadata">
				<source
					src="<?php echo esc_url( $video_source['url'] ); ?>"
					<?php echo ! empty( $video_source['type'] ) ? 'type="' . esc_attr( $video_source['type'] ) . '"' : ''; ?>
				>
			</video>
		</div>
		<?php
		return ob_get_clean();
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
		$block_id          = isset( $attributes['blockID'] ) ? $attributes['blockID'] : '';
		$selected_video_id = ! empty( $attributes['selectedVideoID'] ) ? (int) $attributes['selectedVideoID'] : 0;
		$pre_heading       = isset( $attributes['preHeading'] ) ? $attributes['preHeading'] : '';
		$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$bg_color          = isset( $attributes['bgColor'] ) ? $attributes['bgColor'] : '';
		$heading_color     = isset( $attributes['headingColor'] ) ? $attributes['headingColor'] : '';
		$description_color = isset( $attributes['descriptionColor'] ) ? $attributes['descriptionColor'] : '';
		$content_alignment = isset( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : 'center';
		$show_preheading   = ! empty( $attributes['showPreheading'] );
		$show_heading      = ! isset( $attributes['showheading'] ) || $attributes['showheading'];
		$show_desc         = ! isset( $attributes['showdesc'] ) || $attributes['showdesc'];
		$editor_video_only = ! empty( $attributes['editorVideoOnly'] );

		$main_video = array();
		if ( $selected_video_id > 0 ) {
			$main_video = get_field( 'main_video', $selected_video_id );
		}

		if ( $editor_video_only ) {
			if ( empty( $main_video ) || ! is_array( $main_video ) ) {
				return '';
			}

			return $this->render_video_player( $main_video );
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'dp-video ' . esc_attr( $content_alignment ),
				'id'    => $block_id,
				'style' => ! empty( $bg_color ) ? 'background-color:' . $bg_color : '',
			)
		);

		$heading_style = ! empty( $heading_color ) ? 'style="color:' . esc_attr( $heading_color ) . '"' : '';
		$desc_style    = ! empty( $description_color ) ? 'style="color:' . esc_attr( $description_color ) . '"' : '';

		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
			<div class="container">
				<?php if ( $show_preheading && ! empty( $pre_heading ) ) : ?>
					<div class="dp-video__preheading">
						<span><?php echo wp_kses_post( $pre_heading ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $show_heading && ! empty( $heading ) ) : ?>
					<h1 class="dp-video__heading" <?php echo wp_kses_post( $heading_style ); ?>><?php echo wp_kses_post( $heading ); ?></h1>
				<?php endif; ?>
				<?php if ( $show_desc && ! empty( $description ) ) : ?>
					<p class="dp-video__description" <?php echo wp_kses_post( $desc_style ); ?>><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>
				<?php
				if ( ! empty( $main_video ) && is_array( $main_video ) ) {
					echo $this->render_video_player( $main_video ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
