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
	<?php
	// Plain text pages use the same content card as single posts (template-parts/content.php).
	$vaultrix_article_class = vaultrix_is_plain_content_page()
		? 'vaultrix-post-article u-margin-t50 u-margin-b50 u-margin-t50@max-767 u-margin-b50@max-767 u-bg-white u-padding-a15'
		: 'vaultrix-post-article u-bg-white';
	?>
	<div class="<?php echo esc_attr( $vaultrix_article_class ); ?>">
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
