<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package vaultrix
 */

get_header();
?>

	<main id="primary" class="site-main vaultrix-archive-main vaultrix-main">
		<?php
		$current_user_id = get_current_user_id();
		if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
			$searched_text = filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING );
			$show_terms    = get_terms(
				array(
					'taxonomy'   => 'video-show-category',
					'hide_empty' => false,
					'name__like' => $searched_text,
				)
			);
			?>
			<div class="search-head container">
				<h1><?php esc_html_e( sprintf( 'Search results for: %s', $searched_text ), 'vaultrix' ); ?></h1>
			</div>

			<?php
			if ( $show_terms || have_posts() ) {
				?>
				<div class="search-series-section container">
					<?php if ( $show_terms ) { ?>
						<h2 class="page-title">Series:</h2>
						<div class="search-series-wrapper">
							<?php foreach ( $show_terms as $show_term ) { ?>
								<a href="<?php echo get_term_link( $show_term ); //phpcs:ignore?>">
									<div class="search-series-wrapper__item" id="series-modal" data-series-id="<?php echo esc_attr( $show_term->term_id ); ?>">
										<?php
										$show_img_url = get_field( 'add_feature_image', 'video-show-category_' . $show_term->term_id );
										if ( $show_img_url ) {
											?>
															<img src="<?php echo esc_url( $show_img_url ); ?>" alt="series poster"></img>
												<?php
										} else {
											$placeholder_image = get_field( 'placeholder_image', 'option' );
											if ( ! empty( $placeholder_image ) ) {
												echo wp_get_attachment_image( $placeholder_image, 'full' );
											}
										}
										?>
										<div class="search-series-wrapper__item-overlay">
											<h3><?php echo esc_html( $show_term->name ); ?></h3>
										</div>
									</div>
									</a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>

				<?php if ( have_posts() ) { ?>
					<div class="archive-videos-wrapper container">
						<h2 class="page-title">Videos:</h2>
						<div class="archive-videos-inner-wrapper">
							<?php
							while ( have_posts() ) :
								the_post();

								/**
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-deep_pockets_videos.php and that will be used instead.
								 */
								get_template_part( 'template-parts/content', 'deep_pockets_videos' );

								endwhile;
							?>
						</div>
						<?php
							the_posts_pagination(
								array(
									'prev_text' => __( 'Previous', 'textdomain' ),
									'next_text' => __( 'Next', 'textdomain' ),
								)
							);

						?>
					</div>
				<?php } ?>
				<?php
			} else {
				?>
					<div class="no-article-found-div container">
						<h2 class="no-result-message"><?php esc_html_e( 'No Content found for your search.', 'vaultrix' ); ?></h2>
					</div>
				<?php
			}
		} else {
			if ( is_user_logged_in() ) {
				$pmpro_restriction_text = get_field( 'pmpro_restriction_non_member_user_text', 'option' );
			} else {
				$pmpro_restriction_text = get_field( 'pmpro_restriction_non_login_user_text', 'option' );
			}
			?>
			<div class="dp-restricted-section-message">
				<div class="container">
					<h1 class="member-warning-message"><?php echo esc_html( $pmpro_restriction_text ); ?></h1>
					<h2 class="member-warning-message">New to this site? <span>Register Now</span></h2>
				</div>
			</div>
			<?php
		}
		?>
	</main><!-- #main -->

<?php
get_footer();
