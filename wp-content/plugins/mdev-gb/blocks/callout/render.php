<?php
/**
 * Server-side render for mdev-gb/callout.
 *
 * Core provides $attributes (array), $content (inner blocks markup) and
 * $block (WP_Block) in scope.
 *
 * @package Mdev\Gb
 */

use Mdev\Gb\Blocks\Callout\Tone;

defined( 'ABSPATH' ) || exit;

$mdev_gb_tone    = Tone::sanitize( $attributes['tone'] ?? '' );
$mdev_gb_heading = isset( $attributes['heading'] ) && is_string( $attributes['heading'] ) ? $attributes['heading'] : '';

$mdev_gb_wrapper = get_block_wrapper_attributes(
	[
		'class'      => 'is-tone-' . $mdev_gb_tone,
		'role'       => 'note',
		'aria-label' => Tone::label( $mdev_gb_tone ),
	]
);
?>
<div <?php echo $mdev_gb_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes every attribute value. ?>>
	<?php if ( '' !== $mdev_gb_heading ) : ?>
		<p class="wp-block-mdev-gb-callout__heading"><?php echo wp_kses_post( $mdev_gb_heading ); ?></p>
	<?php endif; ?>
	<div class="wp-block-mdev-gb-callout__content">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block markup is rendered and escaped by each inner block. ?>
	</div>
</div>
