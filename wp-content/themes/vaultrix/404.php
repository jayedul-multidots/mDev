<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package vaultrix
 */

get_header();
?>

	<main id="primary" class="vaultrix-main">
		<section class="error-404 not-found ptf-post-article">
			<div class="container">
				<div class="error-404__content">
					<h1 class="error-404__title"><?php esc_html_e( '404', 'vaultrix' ); ?></h1>
					<p class="error-404__desc"><?php esc_html_e( "We're sorry,", 'vaultrix' ); ?><br><?php esc_html_e( "The page you were looking for doesn't exist anymore.", 'vaultrix' ); ?></p>
					<p class="error-404__btn btn-main">
						<?php
						$current_user_id = get_current_user_id();
						if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
							?>
								<a href="<?php echo esc_url( home_url( '/home' ) ); ?>" rel="home">Back to Home</a>
							<?php
						} else {
							?>
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Back to Home</a>
							<?php
						}
						?>
						
					</p>
				</div><!-- .page-content -->
			</div>
		</section><!-- .error-404 -->
	</main><!-- #main -->
<?php
get_footer();
