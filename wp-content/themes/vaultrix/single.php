<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package vaultrix
 */

get_header();
?>

	<main id="primary" class="site-main vaultrix-main u-padding-t80 u-bg-lightgray">
		<div class="container">
			<div class="u-flex-columns u-flex-columns@max-767">
				<div class="u-flex-column u-flex-basis-70 u-flex-basis-70@max-767">
					<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', get_post_type() );

						the_post_navigation(
							array(
								'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'vaultrix' ) . '</span> <span class="nav-title">%title</span>',
								'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'vaultrix' ) . '</span> <span class="nav-title">%title</span>',
							)
						);

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

						endwhile; // End of the loop.
					?>
				</div>
				<div class="u-flex-column u-flex-basis-30 u-flex-basis-30@max-767">
					<div class="vaultrix-sidebar u-margin-t50@max-767 u-margin-t50 u-margin-b20">
						<?php get_sidebar(); ?>
					</div>
				</div>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_footer();
