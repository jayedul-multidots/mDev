<?php
/**
 * The template for displaying the footer
 *
 * @package vaultrix
 */

$footer_logo        = get_field( 'footer_logo', 'option' );
$footer_description = get_field( 'footer_description', 'option' );
$copyright_text     = get_field( 'copyright_text', 'option' );
?>

<footer id="colophon" class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__inner u-row">
				<div class="site-footer__info">
					<div class="site-footer__info__wrapper">
						<?php if ( ! empty( $footer_logo ) ) { ?>
							<?php
							$current_user_id = get_current_user_id();
							if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
								?>
							<a href="<?php echo esc_url( home_url( '/home' ) ); ?>" class="site-footer__logo" rel="home">
								<?php
							} else {
								?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo" rel="home">
							<?php } ?>
							<?php
							$args = array(
								'width'  => '150',
								'height' => '150',
								'alt'    => 'Footer logo',
							);
							echo wp_get_attachment_image( $footer_logo, 'full', false, $args );
							?>
						</a>
						<?php } ?>
						<p class="site-footer__description">
							<?php
							if ( ! empty( $footer_description ) ) {
								echo esc_html( $footer_description );
							}
							?>
						</p>
					</div>
				</div>
				<div class="site-footer__sidebar-one site-footer__sidebar">
						<?php
						if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
							 dynamic_sidebar( 'footer-sidebar-1' );
						}
						?>
					</div>
				<div class="site-footer__sidebar-two site-footer__sidebar">
					<?php
					if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
						 dynamic_sidebar( 'footer-sidebar-2' );
					}
					?>
				</div>
			</div>
		</div>
		<?php
		while ( have_rows( 'social_icons_group', 'options' ) ) :
			the_row();
			if ( have_rows( 'footer_social_icons' ) ) {
				?>
					<ul class="site-footer__social-icons">
					<?php
					while ( have_rows( 'footer_social_icons' ) ) {
						the_row();
						$social_type = get_sub_field( 'social_type' );
						$social_link = get_sub_field( 'social_link' );
						$link_url    = ! empty( $social_link['url'] ) ? $social_link['url'] : 'javascript:void(0)';
						$link_target = ! empty( $social_link['target'] ) ? $social_link['target'] : '_self';
						$link_text   = ! empty( $social_link['title'] ) ? $social_link['title'] : $social_type;
						?>
						<li>
						<?php
						if ( $social_type === 'instagram' ) {
							?>
							<a href="<?php echo esc_url( $link_url ); ?>" class="<?php echo esc_attr( $social_type ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="<?php echo esc_attr( $link_text ); ?>" rel="noopener">
							<img src="<?php echo esc_url( VAULTRIX_IMAGES_DIR_PATH . 'instagram.svg' ); ?>" height="24" width="24" alt="instagram-icon"><span class="screen-reader-text"><?php echo esc_html( $link_text ); ?></span>
							</a>
								<?php
						} elseif ( $social_type === 'facebook' ) {
							?>
							<a href="<?php echo esc_url( $link_url ); ?>" class="<?php echo esc_attr( $social_type ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="<?php echo esc_attr( $link_text ); ?>" rel="noopener">
							<img src="<?php echo esc_url( VAULTRIX_IMAGES_DIR_PATH . 'facebook.svg' ); ?>" height="24" width="24" alt="facebook-icon"><span class="screen-reader-text"><?php echo esc_html( $link_text ); ?></span>
							</a>
								<?php
						} elseif ( $social_type === 'twitter' ) {
							?>
							<a href="<?php echo esc_url( $link_url ); ?>" class="<?php echo esc_attr( $social_type ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="<?php echo esc_attr( $link_text ); ?>" rel="noopener">
							<img src="<?php echo esc_url( VAULTRIX_IMAGES_DIR_PATH . 'twitter.svg' ); ?>" height="24" width="24" alt="twitter-icon"><span class="screen-reader-text"><?php echo esc_html( $link_text ); ?></span>
							</a>
								<?php
						}
						?>
						</li>
						<?php
					}
					?>
					</ul>
				<?php
			}
			endwhile;
		?>
		<div class="site-footer__bottom">
			<div class="site-copyright-info">
				<?php echo wp_kses_post( $copyright_text ); ?>
			</div>
		</div>
	</div>
	<link rel="preload" href="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/vaultrix/assets/library/css/site-font.min.css" as="style" onload="this.onload=null;this.rel='stylesheet';">
</footer>
<!-- #colophon -->
</div><!-- #page -->
<?php echo get_field( 'custom_js_code_footer', 'option' ); // phpcs:ignore ?>
<?php wp_footer(); ?>

</body>
</html>
