<?php
/**
 * Title: Call to action
 * Slug: mdev-fse/call-to-action
 * Categories: mdev-fse, call-to-action
 * Keywords: cta, contact
 *
 * @package Mdev\Fse
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"12px"}},"backgroundColor":"primary","textColor":"base","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide has-base-color has-primary-background-color has-text-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-x-large-font-size"><?php echo esc_html__( 'Ready to talk?', 'mdev-fse' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button"><?php echo esc_html__( 'Contact us', 'mdev-fse' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
