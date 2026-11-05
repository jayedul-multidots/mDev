<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="vaultrix-post-article u-bg-white">
		<div class="entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'vaultrix' ),
					'after'  => '</div>',
				)
			);
			?>
		</div><!-- .entry-content -->
	</div>	
</article><!-- #post-<?php the_ID(); ?> -->
