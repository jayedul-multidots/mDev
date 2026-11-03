<?php
/**
 * Title: Hero
 * Slug: mdev-fse/hero
 * Categories: mdev-fse, banner
 * Keywords: hero, intro, banner
 * Viewport Width: 1280
 *
 * @package Mdev\Fse
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"level":1,"textAlign":"center"} -->
	<h1 class="wp-block-heading has-text-align-center"><?php echo esc_html__( 'Build something great', 'mdev-fse' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","textColor":"muted","fontSize":"large"} -->
	<p class="has-text-align-center has-muted-color has-text-color has-large-font-size"><?php echo esc_html__( 'A short sentence that explains what this site is about.', 'mdev-fse' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Get started', 'mdev-fse' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
