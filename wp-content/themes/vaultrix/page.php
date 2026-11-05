<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
				endif;

			endwhile; // End of the loop.
		?>
	</main><!-- #main -->
	
	<!-- welcome popup code -->
	<?php
	if ( filter_input( INPUT_GET, 'welcome', FILTER_SANITIZE_STRING ) !== null ) {
		$welcome_popup              = get_field( 'welcome_popup', 'option' );
		$welcome_banner_image       = ! empty( $welcome_popup['banner_image'] ) ? $welcome_popup['banner_image'] : '';
		$welcome_banner_image_style = ! empty( $welcome_banner_image ) ? 'style="background-image:url(' . wp_get_attachment_image_url( $welcome_banner_image, 'full' ) . ');"' : '';
		$welcome_logo_image         = ! empty( $welcome_popup['logo_image'] ) ? $welcome_popup['logo_image'] : '';
		$welcome_heading            = ! empty( $welcome_popup['heading'] ) ? $welcome_popup['heading'] : '';
		$welcome_subheading         = ! empty( $welcome_popup['subheading'] ) ? $welcome_popup['subheading'] : '';
		?>
		<div class="welcome-popup">
			<div class="welcome-popup__wrapper container">
				<div class="welcome-popup-close"></div>
				<div class="welcome-popup__top-image" <?php echo wp_kses_post( $welcome_banner_image_style ); ?>>
					<div class="site-logo">
						<?php
						if ( ! empty( $welcome_logo_image ) ) {
							$args = array(
								'width'  => '215',
								'height' => '26',
								'alt'    => 'site_logo',
							);
							echo wp_get_attachment_image( $welcome_logo_image, 'full', false, $args );
						}
						?>
					</div>
				</div>
				<div class="welcome-popup__detail">
					<?php
					if ( ! empty( $welcome_heading ) || ! empty( $welcome_subheading ) ) {
						if ( ! empty( $welcome_heading ) ) {
							$allowed_html = array(
								'span' => array(
									'class' => array(),
								),
								// Add more allowed HTML tags and attributes as needed
							);
							?>
								<h2 class="welcome-popup__heading"><?php echo wp_kses( $welcome_heading, $allowed_html ); ?></h2>
							<?php
						}
						if ( ! empty( $welcome_subheading ) ) {
							?>
								<h3 class="welcome-popup__sub-heading"><?php echo esc_html( $welcome_subheading ); ?></h3>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}
	get_footer();
