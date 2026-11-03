<?php
/**
 * Title: 404 content
 * Slug: mdev-block/hidden-404
 * Inserter: no
 *
 * @package Mdev\Block
 */

?>
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html__( 'Page not found', 'mdev-block' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'The page you are looking for does not exist or has moved. Try searching instead.', 'mdev-block' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search', 'mdev-block' ); ?>","buttonText":"<?php echo esc_attr__( 'Search', 'mdev-block' ); ?>"} /-->
