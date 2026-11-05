<?php
/**
 * Extra fucntions file
 *
 * @package vaultrix
 */

/**
 * Add slashes for the database insert
 *
 * @param  array|string $value this is value attribute.
 * @return array|string
 */
function vaultrix_post_wp_slash( $value ) {
	return vaultrix_post_addslashes_deep( $value );
}

/**
 * Add deep slashes
 *
 * @param  array|string $value this is value attribute.
 * @return array|string
 */
function vaultrix_post_addslashes_deep( $value ) {
	if ( function_exists( 'map_deep' ) ) {
		return map_deep( $value, 'vaultrix_post_addslashes_to_strings_only' );
	} else {
		return wp_slash( $value );
	}
}

/**
 * Add slashes to string
 *
 * @param  string|array $value this is value attribute.
 * @return array|string
 */
function vaultrix_post_addslashes_to_strings_only( $value ) {
	return is_string( $value ) ? addslashes( $value ) : $value;
}

/**
 * Search form filter.
 * 
 * @param string $form The form HTML.
 * @return string The modified form HTML.
 */
function wpdocs_my_search_form( $form ) {
	$form = '
    <form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
        <div>
            <label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
            <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search..."/>
            <input type="submit" id="searchsubmit" value="' . esc_attr__( 'Search', 'vaultrix' ) . '"/>
            <input type="hidden" name="post_type" value="deep_pockets_videos" />
        </div>
	</form>';

	return $form;
}
add_filter( 'get_search_form', 'wpdocs_my_search_form' );

/**
 * Modify the restriction message for non-member users.
 * 
 * @param string $message The message to modify.
 * @return string The modified message.
 */
function custom_membership_pro_restriction_message_for_non_member_users( $message ) {
	$pmpro_restriction_non_member_user_text = get_field( 'pmpro_restriction_non_member_user_text', 'option' );

	// Replace 'Mastery Bundle' with your desired plan or level name
	$message = "<div class='dp-restricted-section-message'>
					<div class='container'>
						<h1 class='member-warning-message'>" . $pmpro_restriction_non_member_user_text . "</h1>
						<h2 class='member-warning-message'><span>Click here</span> to get your membership now.</h2>
					</div>
				</div>";

	return $message;
}
add_filter( 'pmpro_non_member_text_filter', 'custom_membership_pro_restriction_message_for_non_member_users', 10, 2 );

/**
 * Modify the restriction message for non-login users.
 * 
 * @param string $text The text to modify.
 * @return string The modified text.
 */
function custom_membership_pro_restriction_message_for_non_login_users( $text ) {
	$pmpro_restriction_non_login_user_text = get_field( 'pmpro_restriction_non_login_user_text', 'option' );
	$text                                  = '<div class="dp-restricted-section-message">
				<div class="container">
					<h1 class="member-warning-message">' . $pmpro_restriction_non_login_user_text . '</h1>
					<h2>Already have an account? <span class="login-button">Login Now</span></h2>
					<h2 class="member-warning-message">New to this site? <span>Register Now</span></h2>
				</div>
			</div>';

	return $text;
}
add_filter( 'pmpro_not_logged_in_text_filter', 'custom_membership_pro_restriction_message_for_non_login_users' );

/**
 * Modify the confirmation URL after membership.
 * 
 * @param string $rurl The redirect URL.
 * @return string The redirect URL.
 */
function vaultrix_membership_confirmation_url( $rurl ) {
	$rurl = esc_url( home_url( '/home?welcome=1' ) );

	return $rurl;
}
add_filter( 'pmpro_confirmation_url', 'vaultrix_membership_confirmation_url', 10 );

/**
 * Add the custom PMPro membership level fields.
 * 
 * @param object $level The level object.
 * @return void
 */
function vaultrix_pmpro_membership_level_after_other_settings( $level ) {
	?>
	<div class="pmpro_section_inside">
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="custom_field"><?php esc_html_e( 'Short Description', 'vaultrix' ); ?></label></th>
					<td><input type="text" id="custom_field" name="pmpro_custom_field_<?php echo esc_attr( $level->id ); ?>" value="<?php echo esc_attr( get_option( 'pmpro_custom_field_' . $level->id ) ); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}
add_action( 'pmpro_membership_level_after_general_information', 'vaultrix_pmpro_membership_level_after_other_settings' );

/**
 * Save the custom PMPro membership level fields.
 * 
 * @param int $level_id The level ID.
 * @return void
 */
function vaultrix_save_custom_pmpro_membership_level_fields( $level_id ) {
    $custom_field = sanitize_text_field( $_POST['pmpro_custom_field_'.$level_id] ); // phpcs:ignore
	update_option( 'pmpro_custom_field_' . $level_id, $custom_field );
}
add_action( 'pmpro_save_membership_level', 'vaultrix_save_custom_pmpro_membership_level_fields' );


/**
 * Export functionality for the contact us form entries.
 * 
 * @param string $which The which parameter.
 * @return void
 */
function dp_contact_us_entries_export_button( $which ) {
	global $typenow;

	if ( 'deep_pockets_contact' === $typenow && 'top' === $which ) {
		?>
		<input type="submit" name="dp_export_contact_entries" class="button button-primary" value="<?php esc_attr_e( 'Download CSV' ); ?>" />
		<?php
	}

	if ( 'vaultrix_members' === $typenow && 'top' === $which ) {
		?>
		<input type="submit" name="dp_export_members_entries" class="button button-primary" value="<?php esc_attr_e( 'Download CSV' ); ?>" />
		<?php
	}
}
add_action( 'manage_posts_extra_tablenav', 'dp_contact_us_entries_export_button' );

/**
 * Export the contact entries.
 * 
 * @return void
 */
function dp_export_contact_entries() {
	if ( filter_input( INPUT_GET, 'dp_export_contact_entries', FILTER_SANITIZE_STRING ) !== null ) {
		$args = array(
			'post_type'      => 'deep_pockets_contact',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		global $post;
		$arr_post = get_posts( $args ); // phpcs:ignore
		if ( $arr_post ) {

			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename="contact-form-data.csv"' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			$file = fopen( 'php://output', 'w' );

			fputcsv( $file, array( 'Post ID', 'First Name', 'Email', 'Message' ) ); // phpcs:ignore

			foreach ( $arr_post as $post ) { // phpcs:ignore
				setup_postdata( $post );

				$contact_form_post_id = get_the_ID();
				$email_value          = get_field( 'cf_email_address', $contact_form_post_id );
				$message_value        = get_field( 'cf_message', $contact_form_post_id );

				fputcsv( $file, array( get_the_ID(), get_the_title(), $email_value, $message_value ) ); // phpcs:ignore
			}

			exit();
		}
	}
}

add_action( 'admin_init', 'dp_export_contact_entries' );

/**
 * Export the members entries.
 * 
 * @return void
 */
function dp_export_members_entries() {
	if ( filter_input( INPUT_GET, 'dp_export_members_entries', FILTER_SANITIZE_STRING ) !== null ) {
		$args = array(
			'post_type'      => 'vaultrix_members',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		global $post;
		$arr_post = get_posts( $args ); // phpcs:ignore
		if ( $arr_post ) {

			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename="members-data.csv"' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			$file = fopen( 'php://output', 'w' );

			fputcsv( $file, array( 'Email ID' ) ); // phpcs:ignore

			foreach ( $arr_post as $post ) { // phpcs:ignore
				setup_postdata( $post );

				fputcsv( $file, array( get_the_title() ) ); // phpcs:ignore
			}

			exit();
		}
	}
}

add_action( 'admin_init', 'dp_export_members_entries' );

/**
 * Create a custom post on registration.
 * 
 * @param int $user_id The user ID.
 * @return void
 */
function dp_create_custom_post_on_registration( $user_id ) {

	$user = get_user_by( 'ID', $user_id );

	if ( ! empty( $user->user_email ) ) {
		// Create post data
		$post_data = array(
			'post_title'   => $user->user_email,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => VAULTRIX_MEMBERS_ENTRY_POST_TYPE,
		);

		// Insert the post into the database
		$new_post_id = wp_insert_post( $post_data );
		if ( $new_post_id ) {
			update_post_meta( $new_post_id, '_user_id', $user_id );
		}
	}
}
add_action( 'pmpro_checkout_before_user_auth', 'dp_create_custom_post_on_registration' );

/**
 * Update email in member entries post type on member profile update.
 * 
 * @param int $user_id The user ID.
 * @return void
 */
function dp_change_email_address_on_update( $user_id ) {
	$args        = array(
		'meta_key'       => '_user_id', // phpcs:ignore
		'meta_value' 	 => $user_id, // phpcs:ignore
		'post_type'      => VAULTRIX_MEMBERS_ENTRY_POST_TYPE,
		'post_status'    => 'any',
		'posts_per_page' => 1,
	);
	$member_post = get_posts($args); // phpcs:ignore

	if ( ! empty( $member_post ) ) {
		foreach ( $member_post as $member ) {
			$member_post_id = $member->ID;
			$current_user   = get_user_by( 'ID', $user_id );

			if ( ! empty( $current_user->user_email ) ) {
				$my_post = array(
					'ID'         => $member_post_id,
					'post_title' => $current_user->user_email,
				);
				wp_update_post( $my_post );
			}
		}
	}
}
add_action( 'profile_update', 'dp_change_email_address_on_update' );

// AJAX callback function
function vaultrix_delete_user_callback() {
	// Get current user ID
	$current_user_id = get_current_user_id();
	$response        = array();
	// Check if the user is logged in
	if ( is_user_logged_in() ) {
		// only delete non-admins
		if ( ! user_can( $current_user_id, 'manage_options' ) ) {
			// remove the delete hooks so we don't try to cancel the membership again
			remove_action( 'delete_user', 'pmpro_delete_user' );
			remove_action( 'wpmu_delete_user', 'pmpro_delete_user' );

			// delete the user
			require_once ABSPATH . '/wp-admin/includes/user.php';
			wp_delete_user( $current_user_id );

			// add the delete hooks back in
			add_action( 'delete_user', 'pmpro_delete_user' );
			add_action( 'wpmu_delete_user', 'pmpro_delete_user' );
		}

		$response['success']             = true;
		$response['data']['message']     = __( 'User deleted successfully.', 'vaultrix' );
		$response['data']['redirecturl'] = get_site_url();
	} else {
		$response['success']         = false;
		$response['data']['message'] = __( 'Something went wrong! Please try again.', 'vaultrix' );
	}

	echo wp_json_encode( $response );
	// Always die in functions echoing AJAX content
	wp_die();
}
add_action( 'wp_ajax_delete_user', 'vaultrix_delete_user_callback' );
add_action( 'wp_ajax_nopriv_delete_user', 'vaultrix_delete_user_callback' );

/**
 * Hook to remove the view button from the contact and video post type.
 * 
 * @param array $actions The actions array.
 * @param WP_Post $post The post object.
 * @return array The actions array.
 */
function dp_remove_view_button( $actions, $post ) {
	// Check if the post type is your custom post type
	if ( $post->post_type === 'deep_pockets_videos' || $post->post_type === 'deep_pockets_contact' ) {
		// Remove the 'View' action
		unset( $actions['view'] );
	}
	return $actions;
}

add_filter( 'post_row_actions', 'dp_remove_view_button', 10, 2 );

add_action( 'template_redirect', 'videos_permalink_redirection' );

/**
 * Video and contact post redirect by redirect permalink filter.
 *
 * @since 1.0.0
 */
function videos_permalink_redirection() {
	if ( is_singular( 'deep_pockets_contact' ) || is_singular( 'deep_pockets_videos' ) ) {
		wp_redirect( home_url( '/home' ) );
		exit;
	}
}

/**
 * Redirect for the account pages.
 *
 * @since 1.0.0
 */
function dp_account_pages_redirects() {
	$redirects = array(
		'/membership-account/'                    => home_url( '/' ),
		'/membership-account/membership-billing/' => home_url( '/' ),
		'/membership-account/membership-invoice/' => home_url( '/' ),
		'/membership-account/your-profile/'       => home_url( '/' ),
	);

	// Get the current request URL
	$current_url = $_SERVER['REQUEST_URI']; // phpcs:ignore

	// Check if the current URL needs redirection
	if ( ! is_user_logged_in() && array_key_exists( $current_url, $redirects ) ) {
		// Perform the redirection
		wp_redirect( $redirects[ $current_url ], 301 ); // Use 301 for permanent redirection
		exit();
	}
}
add_action( 'template_redirect', 'dp_account_pages_redirects' );

/**
 * Remove admin bar for subscribers.
 *
 * @param bool $show Whether to show the admin bar.
 * @return bool Whether to show the admin bar.
 * @since 1.0.0
 */
function remove_admin_bar( $show ) {
	if ( is_user_logged_in() && in_array( 'subscriber', (array) wp_get_current_user()->roles, true ) ) {
		return false;
	}

	return $show;
}
add_filter( 'show_admin_bar', 'remove_admin_bar', 1000 );

// Upload avatar image
function vaultrix_handle_avatar_upload() {
	// Check the nonce for security.
	$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
	if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
		if ( isset( $_FILES['avatar'] ) && $_FILES['avatar']['error'] === 0 ) { // phpcs:ignore
			$user_id     = get_current_user_id();
			$upload_dir  = wp_upload_dir();
			$upload_path = $upload_dir['path'];
			$upload_url  = $upload_dir['url'];
			$file_name   = sanitize_file_name( $_FILES['avatar']['name'] ); // phpcs:ignore
			$avatar_path = $upload_path . '/' . $file_name;

			if ( move_uploaded_file( $_FILES['avatar']['tmp_name'], $avatar_path ) ) { // phpcs:ignore
				// Save the avatar URL in user meta data.
				update_user_meta( $user_id, 'custom_avatar_url', $upload_url . '/' . $file_name );

				// Redirect back to the account page or another page.
				echo pmpro_url( 'account' ); // phpcs:ignore
				exit;
			} else {
				// Handle the error if the file wasn't uploaded.
				wp_die( 'Avatar upload failed.' );
			}
		}
	} else {
		// Handle nonce verification failure.
		wp_die( 'Security check failed.' );
	}
}
add_action( 'wp_ajax_handle_avatar_upload', 'vaultrix_handle_avatar_upload' );
add_action( 'wp_ajax_nopriv_handle_avatar_upload', 'vaultrix_handle_avatar_upload' );

// Cache clear on member cancel their membership
function vaultrix_clear_cache_on_membership_cancel() {
	$billing_page_id  = get_option( 'pmpro_billing_page_id' );
	$billing_page_url = get_permalink( $billing_page_id );
	if( isset( $_GET['levelstocancel'] ) && isset( $_GET['confirm'] ) ){ // phpcs:ignore
		if ( function_exists( 'wpcom_vip_purge_edge_cache_for_url' ) ) {
			wpcom_vip_purge_edge_cache_for_url( $billing_page_url );
			wp_cache_flush();
		}
	}
}
add_action( 'template_redirect', 'vaultrix_clear_cache_on_membership_cancel' );

// Cancel subscription message changes
remove_filter( 'pmpro_email_body', 'pmproconpd_pmpro_email_body', 10 );

// Disable the new user notifications or emails
function vaultrix_disable_new_user_notifications() {
	remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
	remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10, 2 );
}

add_action( 'init', 'vaultrix_disable_new_user_notifications' );

/**
 * Removed page cache on video sorting change.
 * 
 * @return void
 */
function vaultrix_clear_cache_on_video_sorting() {
	$args  = array(
		'posts_per_page' => -1,
		'post_type'      => array( 'page' ),
		'post_status'    => 'publish',
	);
	$pages = new WP_Query( $args );

	if ( $pages->have_posts() ) {
		while ( $pages->have_posts() ) {
			$pages->the_post();
			if ( function_exists( 'wpcom_vip_purge_edge_cache_for_url' ) ) {
				wpcom_vip_purge_edge_cache_for_url( get_the_permalink() );
			}
		}
	}
	wp_reset_postdata();
}
add_action( 'PTO/order_update_complete', 'vaultrix_clear_cache_on_video_sorting' );

/**
 * Sorting Order for the archive pages.
 * 
 * @param WP_Query $query The query object.
 * @return void
 */
function vaultrix_sort_order( $query ) {
	if ( is_archive() ) {
		// Set the order
		$query->set( 'order', 'ASC' ); // phpcs:ignore
		// Set the orderby
		$query->set( 'orderby', 'menu_order' ); // phpcs:ignore
	}
};
add_action( 'pre_get_posts', 'vaultrix_sort_order' );

/**
 * Check if the post ID is in the playlist.
 * 
 * @param array $array The array to check.
 * @param int $value The value to check.
 * @return bool Whether the post ID is in the playlist.
 */
function dp_check_post_id_in_playlist( $array, $value ) {
	if ( is_array( $array ) && ! empty( $array ) ) {
		foreach ( $array as $item ) {
			if ( is_array( $item ) && array_key_exists( 'video', $item ) && in_array( $value, $item['video'] ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Format the video duration.
 *
 * @param int $seconds The duration in seconds.
 * @return string The formatted duration.
 */
function format_video_duration( $seconds ) {
	$hours   = floor( $seconds / 3600 );
	$minutes = floor( ( $seconds % 3600 ) / 60 );
	$secs    = $seconds % 60;

	if ( $hours > 0 ) {
		return sprintf(
			'%d hour%s %d min',
			$hours,
			$hours > 1 ? 's' : '',
			$minutes
		);
	}

	if ( $minutes > 0 ) {
		return sprintf( '%d min', $minutes );
	}

	return sprintf( '0:%02d sec', $secs );
}
