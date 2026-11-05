<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

$template_tags_class = \VAULTRIX\Inc\Template_Tags::get_instance();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="vaultrix-post-article u-margin-t50 u-margin-b50 u-margin-t50@max-767 u-margin-b50@max-767 u-bg-white u-padding-a15">
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title u-margin-b10"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php
				$template_tags_class->posted_on();
				$template_tags_class->posted_by();
				?>
			</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<?php $template_tags_class->post_thumbnail(); ?>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->

		<footer class="entry-footer">
			<?php $template_tags_class->entry_footer(); ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
