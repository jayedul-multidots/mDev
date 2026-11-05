<?php
// phpcs:ignoreFile
/**
 * Template: Account
 * Version: 2.0
 *
 * See documentation for how to override the PMPro templates.
 *
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
$atts    = '';
$content = null;
$code    = '';
global $wpdb, $pmpro_msg, $pmpro_msgt, $pmpro_levels, $current_user, $levels;

// Get the edit profile and change password links if 'Member Profile Edit Page' is set.
if ( ! empty( pmpro_getOption( 'member_profile_edit_page_id' ) ) ) {
	$edit_profile_url    = pmpro_url( 'member_profile_edit' );
	$change_password_url = add_query_arg( 'view', 'change-password', pmpro_url( 'member_profile_edit' ) );
} elseif ( ! pmpro_block_dashboard() ) {
	$edit_profile_url    = admin_url( 'profile.php' );
	$change_password_url = admin_url( 'profile.php' );
}

	extract(
		shortcode_atts(
			array(
				'section'  => '',
				'sections' => 'membership,profile,invoices,links',
				'title'    => null,
			),
			$atts
		)
	);

	// did they use 'section' instead of 'sections'?
	if ( ! empty( $section ) ) {
		$sections = $section;
	}

	// Extract the user-defined sections for the shortcode
	$sections = array_map( 'trim', explode( ',', $sections ) );
	ob_start();

	// If multiple sections are being shown, set title to null.
	// Titles can only be changed from the default if only one section is being shown.
	if ( count( $sections ) > 1 ) {
		$title = null;
	}

	// if a member is logged in, show them some info here (1. past invoices. 2. billing information with button to update.)
	$order = new MemberOrder();
	$order->getLastMemberOrder();
	$mylevels     = pmpro_getMembershipLevelsForUser();
	$pmpro_levels = pmpro_getAllLevels( false, true ); // just to be sure - include only the ones that allow signups
	$invoices     = $wpdb->get_results( "SELECT *, UNIX_TIMESTAMP(CONVERT_TZ(timestamp, '+00:00', @@global.time_zone)) as timestamp FROM $wpdb->pmpro_membership_orders WHERE user_id = '$current_user->ID' AND status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC LIMIT 6" );
	?>
	<div class="dp-myaccount-wrap-main prjdp-form">
		<div class="dp-myaccount-wrap">
			<div class="dp-myaccount-left">
				<div class="dp-myaccount-box">
					<?php
					if ( has_nav_menu( 'my-account-menu' ) ) {
						wp_nav_menu( array( 'theme_location' => 'my-account-menu' ) );
					}
					?>
				</div>
				<?php echo sprintf( '<a id="pmpro_actionlink-logout" class="btn-main btn-transparent" href="%s">%s</a>', esc_url( wp_logout_url() ), esc_html__( 'Log Out', 'vaultrix' ) ); ?>
			</div>
			<div class="dp-myaccount-right">
				<div id="pmpro_account">

					<?php if ( in_array( 'profile', $sections ) ) { ?>
						<div id="pmpro_account-profile" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_box', 'pmpro_account-profile' ) ); ?>">
							<div class="dp-account-title-wrap">
								<?php
								if ( '' !== $title ) { // Check if title is being forced to not show.
									// If a custom title was not set, use the default. Otherwise, show the custom title.
									?>
									<h2><?php echo esc_html( null === $title ? __( 'My Account', 'vaultrix' ) : $title ); ?></h2>
									<?php
								}
								echo sprintf( '<a id="pmpro_actionlink-profile" href="%s">%s</a>', esc_url( $edit_profile_url ), esc_html__( 'Edit Profile', 'vaultrix' ) );
								?>
							</div>
							<?php do_action( 'pmpro_account_bullets_top' ); ?>
							<div class="dp-account-fields-wrap">
								<div class="dp-account-field">
									<label><?php esc_html_e( 'Profile Picture', 'vaultrix' ); ?></label>
									<?php
										$custom_avatar_url = get_user_meta($current_user->ID, 'custom_avatar_url', true);

										echo "<div class='profile-picture-wrapper'>";
											if ($custom_avatar_url) {
												echo '<img src="' . $custom_avatar_url . '" alt="Custom Avatar" />';
											} else {
												$placeholder_image = get_field( 'profile_placeholder_image', 'option' );
												if ( ! empty( $placeholder_image ) ) {
													echo wp_get_attachment_image( $placeholder_image, 'full' );
												}
											}
										echo "<a href=". $edit_profile_url ."><i class='far fa-edit'></i><p class='screen-reader-text'>Edit Your Profile</p></a>";
										// echo "<span class='change-profile-picture'><i class='fas fa-edit'></i></span>";
										echo "</div>";
									?>
								</div>
								<?php if($current_user->user_firstname) { ?>
									<div class="dp-account-field">
										<label><?php esc_html_e( 'Full Name', 'vaultrix' ); ?></label>
										<input type="text" readonly="readonly" name="user_email" id="user_email" value="<?php echo esc_html( $current_user->user_firstname . ' ' . $current_user->user_lastname );?>" class="input">
									</div>
								<?php } ?>
								<div class="dp-account-field email">
									<label for="user_email"><?php esc_html_e( 'Email', 'vaultrix' ); ?></label>
									<input type="text" readonly="readonly" name="user_email" id="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" class="input">
								</div>
							</div>
							<?php do_action( 'pmpro_account_bullets_bottom' ); ?>
							<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_actionlinks' ) ); ?>">
								<?php

								// Build the links to return.
								$pmpro_profile_action_links = array();
								if ( ! empty( $edit_profile_url ) ) {
									//$pmpro_profile_action_links['edit-profile'] = sprintf( '<a id="pmpro_actionlink-profile" href="%s">%s</a>', esc_url( $edit_profile_url ), esc_html__( 'Edit Profile', 'vaultrix' ) );
								}

								printf( '<a id="pmpro_actionlink-delete-account" href="javascript:void(0);" class="dp-delete-button btn-main btn-transparent">%s</a>', esc_html__( 'Delete Account', 'vaultrix' ) );

								printf( '<a id="pmpro_actionlink-change-password" href="javascript:void(0);" class="dp-change-password-button btn-main change-password-button">%s</a>', esc_html__( 'Change Password', 'vaultrix' ) );
								?>
							</div>
						</div> <!-- end pmpro_account-profile -->
					<?php } ?>

				</div> <!-- end pmpro_account -->
				<?php echo sprintf( '<a id="pmpro_actionlink-logout" class="dp-mobile-logout btn-main btn-transparent" href="%s">%s</a>', esc_url( wp_logout_url() ), esc_html__( 'Log Out', 'vaultrix' ) ); ?>
			</div>
		</div>
	</div>
	<?php
