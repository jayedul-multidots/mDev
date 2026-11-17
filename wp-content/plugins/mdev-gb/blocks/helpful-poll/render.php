<?php
/**
 * Server-side render for mdev-gb/helpful-poll.
 *
 * Core provides $attributes (array), $content (string) and $block (WP_Block)
 * in scope. Renders nothing unless the post accepts votes (published, public,
 * not password protected), so drafts and previews never show the poll.
 *
 * Escaping: the question comes from RichText and keeps its inline formatting
 * through wp_kses_post(); button labels are plain text (esc_html()); the
 * context is JSON-encoded and attribute-escaped by
 * wp_interactivity_data_wp_context(). Directive output (data-wp-text) is
 * escaped by the Interactivity API on the server and set as textContent in
 * the browser.
 *
 * @package Mdev\Gb
 */

use Mdev\Gb\HelpfulPoll\PollView;
use Mdev\Gb\HelpfulPoll\VotablePolicy;

defined( 'ABSPATH' ) || exit;

$mdev_gb_poll_view = new PollView( new VotablePolicy() );
$mdev_gb_poll      = $mdev_gb_poll_view->build(
	is_array( $attributes ) ? $attributes : [],
	$mdev_gb_poll_view->resolve_post_id( $block instanceof WP_Block ? $block->context : [] )
);

if ( null === $mdev_gb_poll ) {
	return;
}

$mdev_gb_poll_id      = wp_unique_id( 'mdev-gb-helpful-poll-' );
$mdev_gb_poll_wrapper = get_block_wrapper_attributes();
?>
<div
	<?php echo $mdev_gb_poll_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes every attribute value. ?>
	data-wp-interactive="<?php echo esc_attr( PollView::STORE ); ?>"
	<?php echo wp_interactivity_data_wp_context( $mdev_gb_poll['context'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_interactivity_data_wp_context() JSON-encodes and escapes the attribute. ?>
	data-wp-init="callbacks.restore"
>
	<div
		class="wp-block-mdev-gb-helpful-poll__ask"
		role="group"
		aria-labelledby="<?php echo esc_attr( $mdev_gb_poll_id ); ?>"
		data-wp-bind--hidden="context.hasVoted"
	>
		<p id="<?php echo esc_attr( $mdev_gb_poll_id ); ?>" class="wp-block-mdev-gb-helpful-poll__question">
			<?php echo wp_kses_post( $mdev_gb_poll['question'] ); ?>
		</p>
		<div class="wp-block-mdev-gb-helpful-poll__buttons">
			<button
				type="button"
				class="wp-block-mdev-gb-helpful-poll__button wp-element-button"
				data-wp-on--click="actions.voteYes"
				data-wp-bind--disabled="context.isSubmitting"
			><?php echo esc_html( $mdev_gb_poll['yes'] ); ?></button>
			<button
				type="button"
				class="wp-block-mdev-gb-helpful-poll__button wp-element-button"
				data-wp-on--click="actions.voteNo"
				data-wp-bind--disabled="context.isSubmitting"
			><?php echo esc_html( $mdev_gb_poll['no'] ); ?></button>
		</div>
		<p class="wp-block-mdev-gb-helpful-poll__error" role="alert" data-wp-text="context.error"></p>
	</div>
	<p class="wp-block-mdev-gb-helpful-poll__thanks" role="status" tabindex="-1" data-wp-text="context.message"></p>
</div>
