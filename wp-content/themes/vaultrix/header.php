<?php
/**
 * The header for our theme
 *
 * @package vaultrix
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php echo get_field( 'custom_js_code_head', 'option' ); // phpcs:ignore ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php echo get_field( 'custom_js_code_after_body_open', 'option' ); // phpcs:ignore ?>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
$header_logo = get_field( 'header_logo', 'option' );
?>
<div class="popup-overlay"></div>
<div class="popup-modal">
	<div class="popup-wrapper"></div>
</div>
<div class="video-watch-popup"></div>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'vaultrix' ); ?></a>

	<header id="masthead" class="vaultrix-header site-header">
		<div class="container">
			<div class="vaultrix-header__wrapper">
				<div class="vaultrix-header__logo__wrapper">
					<div class="site-branding vaultrix-header__logo">
						<?php
						$current_user_id = get_current_user_id();
						if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) ) {
							?>
							<a href="<?php echo esc_url( home_url( '/home' ) ); ?>" rel="home">
							<?php
						} else {
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php
						}
						?>
							<?php
							if ( ! empty( $header_logo ) ) {
								$args = array(
									'width'  => '215',
									'height' => '26',
									'alt'    => 'Header logo',
								);
								echo wp_get_attachment_image( $header_logo, 'full', false, $args );
							}
							?>
						</a>
					</div><!-- .site-branding -->
				</div>
				<div class="vaultrix-header__menu__wrapper">
					<nav id="site-navigation" class="main-navigation vaultrix-header__navigation">
						<button class="menu-toggle " aria-label="Menu" aria-controls="primary-menu" aria-expanded="false">
						<img class="menu-open" src="<?php echo esc_url( VAULTRIX_IMAGES_DIR_PATH . 'menu-bars.png' ); ?>" width="16" height="16" alt="bar"/>
						<img class="menu-close" src="<?php echo esc_url( VAULTRIX_IMAGES_DIR_PATH . 'menu-close.png' ); ?>" width="16" height="16" alt="bar-close"/>
					</button>
						<div class="vaultrix-menu">
							<div class="search-box mobile-search">
								<form action="<?php echo esc_url( site_url( '/' ) ); ?>" method="get" id="searchfm" class="searchForm">
									<label for="s">
										<i class="fas fa-search"></i>
										<label for="searchinput" class="screen-reader-text">Search:</label>
										<input type="search" name="s" placeholder="Search..." value="<?php echo esc_attr( get_search_query() ); ?>" class="search searchstart" id="searchinput">
									</label>
									<input type="hidden" name="post_type" value="deep_pockets_videos" />
								</form>
							</div>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => is_user_logged_in() ? 'logged-in-menu' : 'logged-out-menu',
								)
							);

							if ( ! is_user_logged_in() ) {
								$sign_up_button        = get_field( 'sign_up_button', 'option' );
								$sign_up_button_target = ! empty( $sign_up_button['target'] ) ? $sign_up_button['target'] : '_self';
								$sign_up_button_url    = ! empty( $sign_up_button['url'] ) ? $sign_up_button['url'] : '';
								$sign_up_button_title  = ! empty( $sign_up_button['title'] ) ? $sign_up_button['title'] : '';

								$login_button        = get_field( 'login_button', 'option' );
								$login_button_target = ! empty( $login_button['target'] ) ? $login_button['target'] : '';
								$login_button_url    = ! empty( $login_button['url'] ) ? $login_button['url'] : '';
								$login_button_title  = ! empty( $login_button['title'] ) ? $login_button['title'] : '';

								if ( ( ! empty( $sign_up_button_title ) && ! empty( $sign_up_button_url ) ) || ( ! empty( $login_button_title ) && ! empty( $login_button_url ) ) ) {
									?>
										<div class="vaultrix-header__btn-wrap mobile">
											<?php
											if ( ! empty( $sign_up_button_title ) && ! empty( $sign_up_button_url ) ) {
												?>
													<div class="btn-main signup-button">
														<a href="<?php echo esc_url( $sign_up_button_url ); ?>" target="<?php echo esc_attr( $sign_up_button_target ); ?>"><?php echo esc_html( $sign_up_button_title ); ?></a>
													</div>
												<?php
											}
											if ( ! empty( $login_button_title ) && ! empty( $login_button_url ) ) {
												?>
													<div class="btn-main btn-transparent login-button">
														<a href="<?php echo esc_url( $login_button_url ); ?>" target="<?php echo esc_attr( $login_button_target ); ?>"><?php echo esc_html( $login_button_title ); ?></a>
													</div>
												<?php
											}
											?>
										</div>
									<?php
								}
							} else {
								?>
								<div>
									<a href="javascript:void(0);">
										<span class="icon-search" aria-hidden="true"></span>
										<span class="screen-reader-text">Search</span>
									</a>
								</div>
								<?php
							}
							?>
						</div>
					</nav><!-- #site-navigation -->
				</div>
				<?php
				if ( ! is_user_logged_in() ) {
					$sign_up_button        = get_field( 'sign_up_button', 'option' );
					$sign_up_button_target = ! empty( $sign_up_button['target'] ) ? $sign_up_button['target'] : '_self';
					$sign_up_button_url    = ! empty( $sign_up_button['url'] ) ? $sign_up_button['url'] : '';
					$sign_up_button_title  = ! empty( $sign_up_button['title'] ) ? $sign_up_button['title'] : '';

					$login_button        = get_field( 'login_button', 'option' );
					$login_button_target = ! empty( $login_button['target'] ) ? $login_button['target'] : '';
					$login_button_url    = ! empty( $login_button['url'] ) ? $login_button['url'] : '';
					$login_button_title  = ! empty( $login_button['title'] ) ? $login_button['title'] : '';

					if ( ( ! empty( $sign_up_button_title ) && ! empty( $sign_up_button_url ) ) || ( ! empty( $login_button_title ) && ! empty( $login_button_url ) ) ) {
						?>
							<div class="vaultrix-header__btn-wrap desktop ">
								<?php
								if ( ! empty( $sign_up_button_title ) && ! empty( $sign_up_button_url ) ) {
									?>
										<div class="btn-main signup-button">
											<a href="<?php echo esc_url( $sign_up_button_url ); ?>" target="<?php echo esc_attr( $sign_up_button_target ); ?>"><?php echo esc_html( $sign_up_button_title ); ?></a>
										</div>
									<?php
								}
								if ( ! empty( $login_button_title ) && ! empty( $login_button_url ) ) {
									?>
										<div class="btn-main btn-transparent login-button">
											<a href="<?php echo esc_url( $login_button_url ); ?>" target="<?php echo esc_attr( $login_button_target ); ?>"><?php echo esc_html( $login_button_title ); ?></a>
										</div>
									<?php
								}
								?>
							</div>
						<?php
					}
				} else {
					?>
						<div class="header-admin-details desktop">
							<div class="header-search-section">
								<button type="submit" id="" aria-label="Search" class="header-serch-icon"></button>
								<div class="searchform-fly-overlay">
									<div class="close-button">
										<a href="javascript:void(0);" class="close-btn" title="Close">
											<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
												<path id="Path_2521" data-name="Path 2521" d="M40,2.357,37.643,0,20,17.643,2.357,0,0,2.357,17.643,20,0,37.643,2.357,40,20,22.357,37.643,40,40,37.643,22.357,20Z" fill="#242c3c"></path>
											</svg>
										</a>
									</div>
									<div class="search-box">
										<form action="<?php echo esc_url( site_url( '/' ) ); ?>" method="get" id="searchfm" class="searchForm">
											<label for="s">
												Search...
												<input type="search" name="s" placeholder="Search..." value="<?php echo esc_attr( get_search_query() ); ?>" class="search searchstart" id="searchinput">
											</label>
											<input type="hidden" name="post_type" value="deep_pockets_videos" />
										</form>
									</div>
								</div>
							</div>
							<div class="header-admin-img">
								<a href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>">
								<?php
									$custom_avatar_url = get_user_meta( get_current_user_id(), 'custom_avatar_url', true );
								if ( $custom_avatar_url ) {
									echo '<img src="' . esc_url( $custom_avatar_url ) . '" alt="Custom Avatar" />';
								} else {
									$placeholder_image = get_field( 'profile_placeholder_image', 'option' );
									if ( ! empty( $placeholder_image ) ) {
										$args = array(
											'alt' => 'Custom Avatar',
										);
										echo wp_get_attachment_image( $placeholder_image, 'full', false, $args );
									}
								}
								?>
								</a>
							</div>
						</div>
					<?php
				}
				?>
			</div>
		</div>

		<?php
		$levels        = pmpro_getAllLevels();
		$level_type    = '';
		$pricing_popup = get_field( 'pricing_popup', 'option' );
		$signup_popup  = get_field( 'signup_popup', 'option' );
		if ( ! is_user_logged_in() ) {
			?>
			<!-- Pricing/Sign Up Popup -->
			<div class="popup">
				<!-- Pricing Popup -->
				<?php
				if ( $pricing_popup ) {
					$pricing_heading     = ! empty( $pricing_popup['heading'] ) ? $pricing_popup['heading'] : '';
					$pricing_description = ! empty( $pricing_popup['description'] ) ? $pricing_popup['description'] : '';
					$counter             = is_array( $levels ) ? count( $levels ) : 1;
					?>
					<div class="pricing-popup">
						<div class="popup__wrapper container">
							<?php
							if ( ! empty( $pricing_heading ) || ! empty( $pricing_description ) ) {
								?>
									<div class="popup__head">
										<div class="popup-close"></div>
										<?php
										if ( ! empty( $pricing_heading ) ) {
											?>
											<h2 class="popup__heading"><?php echo esc_html( $pricing_heading ); ?></h2>
											<?php
										}
										if ( ! empty( $pricing_description ) ) {
											?>
											<p class="popup__desc"><?php echo esc_html( $pricing_description ); ?></p>
											<?php
										}
										?>
									</div>	
								<?php
							}
							echo '<div class="popup-content col-' . esc_html( $counter ) . '">';
							if ( ! empty( $levels ) ) {
								foreach ( $levels as $level ) {
									if ( $level->cycle_period === 'Year' && $level->cycle_number === '1' ) {
										$level_type = 'Annually';
									} elseif ( $level->cycle_period === 'Month' && $level->cycle_number === '1' ) {
										$level_type = 'Monthly';
									}
									$checkout_page_id  = get_option( 'pmpro_checkout_page_id' );
									$checkout_url      = add_query_arg( 'level', $level->id, get_the_permalink( $checkout_page_id ) );
									$short_description = ! empty( get_option( 'pmpro_custom_field_' . $level->id ) ) ? get_option( 'pmpro_custom_field_' . $level->id ) : '';

									echo '<div class="membership-card">';
										echo '<div class="membership-head">';
											echo '<h2 class="heading">' . esc_html( $level->name ) . '</h2>';
									if ( ! empty( $short_description ) ) {
										echo '<p class="head-description">' . esc_html( $short_description ) . '</p>';
									}
										echo '</div>';

										echo '<div class="membership-content">';
											echo '<div class="price"><sup>$ </sup><span>' . esc_html( $level->initial_payment ) . '</span> <sub>/ ' . esc_html( $level_type ) . '</sub></div>';
											echo '<div class="description">' . wp_kses_post( $level->description ) . '</div>';
											echo '<div class="btn-main"><a href="' . esc_url( $checkout_url ) . '">Choose Plan</a></div>';
										echo '</div>';

									echo '</div>';
									// Add any additional HTML or styling as needed
									$counter++;
								}
							}
							echo '</div>';
							?>
						</div>
					</div>
					<?php
				}
				?>
				<!-- Signup Popup -->
				<?php
				if ( $signup_popup ) {
					$signup_banner_image     = ! empty( $signup_popup['banner_image'] ) ? $signup_popup['banner_image'] : '';
					$sign_banner_image_style = ! empty( $signup_banner_image ) ? 'style="background-image:url(' . wp_get_attachment_image_url( $signup_banner_image, 'full' ) . ');"' : '';
					$signup_logo_image       = ! empty( $signup_popup['logo_image'] ) ? $signup_popup['logo_image'] : '';
					$signup_heading          = ! empty( $signup_popup['heading'] ) ? $signup_popup['heading'] : '';
					$signup_subheading       = ! empty( $signup_popup['subheading'] ) ? $signup_popup['subheading'] : '';
					$signup_description      = ! empty( $signup_popup['description'] ) ? $signup_popup['description'] : '';
					$counter                 = is_array( $levels ) ? count( $levels ) : 1;
					?>
					<div class="signup-popup">
						<div class="popup__wrapper container">
							<div class="popup-close"></div>
							<div class="top-image" <?php echo wp_kses_post( $sign_banner_image_style ); ?>>
								<div class="site-logo">
									<?php
									if ( ! empty( $signup_logo_image ) ) {
										$args = array(
											'width'  => '215',
											'height' => '26',
											'alt'    => 'site_logo',
										);
										echo wp_get_attachment_image( $signup_logo_image, 'full', false, $args );
									}
									?>
								</div>
							</div>
							<div class="plan-detail">
								<?php
								if ( ! empty( $signup_heading ) || ! empty( $signup_subheading ) || ! empty( $signup_description ) ) {
									?>
										<div class="popup__head">
											<?php
											if ( ! empty( $signup_heading ) ) {
												?>
												<h2 class="popup__heading"><?php echo esc_html( $signup_heading ); ?></h2>
												<?php
											}
											if ( ! empty( $signup_subheading ) ) {
												?>
												<h3 class="popup__sub-heading"><?php echo esc_html( $signup_subheading ); ?></h3>
												<?php
											}
											if ( ! empty( $signup_description ) ) {
												?>
												<p class="popup__desc"><?php echo esc_html( $signup_description ); ?></p>
												<?php
											}
											?>
										</div>	
									<?php
								}
								echo '<div class="popup-content col-' . esc_html( $counter ) . '">';
								if ( ! empty( $levels ) ) {
									foreach ( $levels as $level ) {
										if ( $level->cycle_period === 'Year' && $level->cycle_number === '1' ) {
											$level_type = 'Annually';
										} elseif ( $level->cycle_period === 'Month' && $level->cycle_number === '1' ) {
											$level_type = 'Monthly';
										}
										$checkout_page_id  = get_option( 'pmpro_checkout_page_id' );
										$checkout_url      = add_query_arg( 'level', $level->id, get_the_permalink( $checkout_page_id ) );
										$short_description = ! empty( get_option( 'pmpro_custom_field_' . $level->id ) ) ? get_option( 'pmpro_custom_field_' . $level->id ) : '';

										echo '<div class="membership-card">';
											echo '<div class="membership-head">';
												echo '<h2 class="heading">' . esc_html( $level->name ) . '</h2>';
										if ( ! empty( $short_description ) ) {
											echo '<p class="head-description">' . esc_html( $short_description ) . '</p>';
										}
											echo '</div>';

											echo '<div class="membership-content">';
												echo '<div class="price"><sup>$ </sup><span>' . esc_html( $level->initial_payment ) . '</span> <sub>/ ' . esc_html( $level_type ) . '</sub></div>';
												echo '<div class="description">' . wp_kses_post( $level->description ) . '</div>';
												echo '<div class="btn-main"><a href="' . esc_url( $checkout_url ) . '">Choose Plan</a></div>';
											echo '</div>';

										echo '</div>';
										// Add any additional HTML or styling as needed
									}
								}
								echo '</div>';
								?>
							</div>
						</div>
					</div>
					<?php
				}
				do_action( 'deep_pockets_popup_content' );
				?>
			</div>
			<?php
		} else {
			?>
			<!-- Change password Popup -->
			<div class="popup">
				<?php
				if ( $signup_popup ) {
					$signup_banner_image     = ! empty( $signup_popup['banner_image'] ) ? $signup_popup['banner_image'] : '';
					$sign_banner_image_style = ! empty( $signup_banner_image ) ? 'style="background-image:url(' . wp_get_attachment_image_url( $signup_banner_image, 'full' ) . ');"' : '';
					$signup_logo_image       = ! empty( $signup_popup['logo_image'] ) ? $signup_popup['logo_image'] : '';
					$signup_heading          = ! empty( $signup_popup['heading'] ) ? $signup_popup['heading'] : '';
					$signup_subheading       = ! empty( $signup_popup['subheading'] ) ? $signup_popup['subheading'] : '';
					$signup_description      = ! empty( $signup_popup['description'] ) ? $signup_popup['description'] : '';
					$counter                 = is_array( $levels ) ? count( $levels ) : 1;
					?>
						<div class="signup-popup">
							<div class="popup__wrapper container">
								<div class="popup-close"></div>
								<div class="top-image" <?php echo wp_kses_post( $sign_banner_image_style ); ?>>
									<div class="site-logo">
									<?php
									if ( ! empty( $signup_logo_image ) ) {
										$args = array(
											'width'  => '215',
											'height' => '26',
											'alt'    => 'site_logo',
										);
										echo wp_get_attachment_image( $signup_logo_image, 'full', false, $args );
									}
									?>
									</div>
								</div>
								<div class="plan-detail">
									<?php
									if ( ! empty( $signup_heading ) || ! empty( $signup_subheading ) || ! empty( $signup_description ) ) {
										?>
											<div class="popup__head">
												<?php
												if ( ! empty( $signup_heading ) ) {
													?>
													<h2 class="popup__heading"><?php echo esc_html( $signup_heading ); ?></h2>
													<?php
												}
												if ( ! empty( $signup_subheading ) ) {
													?>
													<h3 class="popup__sub-heading"><?php echo esc_html( $signup_subheading ); ?></h3>
													<?php
												}
												if ( ! empty( $signup_description ) ) {
													?>
													<p class="popup__desc"><?php echo esc_html( $signup_description ); ?></p>
													<?php
												}
												?>
											</div>	
										<?php
									}
									echo '<div class="popup-content col-' . esc_html( $counter ) . '">';
									if ( ! empty( $levels ) ) {
										foreach ( $levels as $level ) {
											if ( $level->cycle_period === 'Year' && $level->cycle_number === '1' ) {
												$level_type = 'Annually';
											} elseif ( $level->cycle_period === 'Month' && $level->cycle_number === '1' ) {
												$level_type = 'Monthly';
											}
											$checkout_page_id  = get_option( 'pmpro_checkout_page_id' );
											$checkout_url      = add_query_arg( 'level', $level->id, get_the_permalink( $checkout_page_id ) );
											$short_description = ! empty( get_option( 'pmpro_custom_field_' . $level->id ) ) ? get_option( 'pmpro_custom_field_' . $level->id ) : '';

											echo '<div class="membership-card">';
												echo '<div class="membership-head">';
													echo '<h2 class="heading">' . esc_html( $level->name ) . '</h2>';
											if ( ! empty( $short_description ) ) {
												echo '<p class="head-description">' . esc_html( $short_description ) . '</p>';
											}
												echo '</div>';

												echo '<div class="membership-content">';
													echo '<div class="price"><sup>$ </sup><span>' . esc_html( $level->initial_payment ) . '</span> <sub>/ ' . esc_html( $level_type ) . '</sub></div>';
													echo '<div class="description">' . wp_kses_post( $level->description ) . '</div>';
													echo '<div class="btn-main"><a href="' . esc_url( $checkout_url ) . '">Choose Plan</a></div>';
												echo '</div>';

											echo '</div>';
											// Add any additional HTML or styling as needed
										}
									}
									echo '</div>';
									?>
								</div>
							</div>
						</div>
						<?php
				}
					do_action( 'deep_pockets_loginuser_popup_content' );
				?>
			</div>
			<?php
		}
		?>
	</header><!-- #masthead -->
