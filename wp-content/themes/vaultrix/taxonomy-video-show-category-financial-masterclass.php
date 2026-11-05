<?php
/**
 * The template for displaying season wise archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vaultrix
 */

get_header();

?>
	<main id="primary" class="site-main vaultrix-archive-main vaultrix-main">
		<?php
		$current_user_id = get_current_user_id();
		if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
			?>
				<div class="container">
					<div class="all-series__content-wrapper">
						<div class="all-series__heading-section">
							<h3 class="all-series__heading"><?php esc_html_e( 'All Financial Masterclass', 'vaultrix' ); ?></h3>
						</div>
						<div class="all-series__series-wrapper">
							<?php
							$series_term_id = get_term_by( 'slug', 'financial-masterclass', 'video-show-category' );

							// Get all child terms under the parent term
							$child_terms = get_terms(
								array(
									'taxonomy' => 'video-show-category', // Replace 'your_taxonomy' with the actual taxonomy name
									'parent'   => $series_term_id->term_id,
								)
							);

							if ( ! empty( $child_terms ) ) {
								foreach ( $child_terms as $child_term ) {
									$video_img_url = get_field( 'add_feature_image', 'video-show-category_' . $child_term->term_id );
									?>
									<div class="all-series__video-series">
										<div class="all-series__video-series-inner-wrapper" id="series-modal">
											<a href="<?php echo get_term_link( $child_term ); //phpcs:ignore?>"> 
												<?php
												if ( $video_img_url ) {
													?>
													<img src="<?php echo esc_url( $video_img_url ); ?>" alt="series poster" ></img>
													<?php
												} else {
													$placeholder_image = get_field( 'placeholder_image', 'option' );
													if ( ! empty( $placeholder_image ) ) {
														echo wp_get_attachment_image( $placeholder_image, 'full' );
													}
												}
												?>
												<div class="all-series__video-series-caption">
													<h3><?php echo esc_html( $child_term->name ); ?></h3>
													<?php
													$seasons         = get_terms(
														array(
															'taxonomy'   => 'video-season-category',
															'hide_empty' => false, // Retrieve even if they have no posts
														)
													);
													$season_counter  = 0;
													$episode_counter = 0;
													foreach ( $seasons as $season ) {
														$season_posts = new \WP_Query(
															array(
																'post_type' => 'deep_pockets_videos',
																'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
																	'relation' => 'AND', // Add an 'AND' relation between the conditions
																	array(
																		'taxonomy' => 'video-season-category',
																		'field'    => 'term_id',
																		'terms'    => $season->term_id,
																	),
																	array(
																		'taxonomy' => 'video-show-category',
																		'field'    => 'slug',
																		'terms'    => 'financial-masterclass',
																	),
																	array(
																		'taxonomy' => 'video-show-category',
																		'field'    => 'slug',
																		'terms'    => $child_term->slug,
																	),
																),
															)
														);
														if ( $season_posts->have_posts() ) {
															$season_counter++;
															while ( $season_posts->have_posts() ) {
																$season_posts->the_post();
																$episode_counter++;
															}
														}
													}
													?>
													<div class="season-details-wrapper">
														<span class="season-details"><?php echo esc_html( $season_counter ); ?> SEASONS</span>
														<span class="season-details dot-seprator"> • </span>
														<span class="season-details"><?php echo esc_html( $episode_counter ); ?> EPISODES</span>
													</div>
												</div>
											</a>
										</div>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			<?php
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
