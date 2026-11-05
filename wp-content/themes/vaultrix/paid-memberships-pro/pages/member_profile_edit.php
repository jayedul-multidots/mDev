<?php
// phpcs:ignoreFile
/**
 * Template: Member Profile Edit
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
// Get the current action for the view.
if ( ! empty( $_REQUEST['view'] ) ) {
	$view = sanitize_text_field( $_REQUEST['view'] );
} else {
	$view = null;
}
global $current_user;

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
            <?php echo sprintf( '<a id="pmpro_actionlink-logout"  class="btn-main btn-transparent" href="%s">%s</a>', esc_url( wp_logout_url() ), esc_html__( 'Log Out', 'vaultrix' ) ); ?>
        </div>
        <div class="dp-myaccount-right">
            <?php
            if ( ! empty( $view ) && $view == 'change-password' ) {
                // Display the Change Password form.
                pmpro_change_password_form();
            } else {
                if ( ! is_user_logged_in() ) {
                    echo '<div class="' . pmpro_get_element_class( 'pmpro_message pmpro_alert', 'pmpro_alert' ) . '"><a href="' . esc_url( pmpro_login_url() ) . '">' . esc_html__( 'Log in to edit your profile.', 'vaultrix' ) . '</a></div>';
                    return;
                }
            
                // Saving profile updates.
                if ( isset( $_POST['action'] ) && $_POST['action'] == 'update-profile' && $current_user->ID == $_POST['user_id'] && wp_verify_nonce( sanitize_key( $_POST['update_user_nonce'] ), 'update-user_' . $current_user->ID ) ) {
                    $update           = true;
                    $user     		  = new stdClass;
                    $user->ID         = intval( $_POST[ 'user_id' ] );
                    do_action( 'pmpro_personal_options_update', $user->ID );
                } else {
                    $update = false;
                }
            
                if ( $update ) {
            
                    $errors = array();
            
                    // Get all values from the $_POST, sanitize them, and build the $user object.
                    if ( isset( $_POST['user_email'] ) ) {
                        $user->user_email = sanitize_text_field( wp_unslash( $_POST['user_email'] ) );
                    }
                    if ( isset( $_POST['first_name'] ) ) {
                        $user->first_name = sanitize_text_field( $_POST['first_name'] );
                    }
                    if ( isset( $_POST['last_name'] ) ) {
                        $user->last_name = sanitize_text_field( $_POST['last_name'] );
                    }
                    if ( isset( $_POST['display_name'] ) ) {
                        $user->display_name = sanitize_text_field( $_POST['display_name'] );
                        $user->nickname = $user->display_name;
                    }
            
                    // Validate display name.
                    if ( empty( $user->display_name ) ) {
                        $errors[] = __( 'Please enter a display name.', 'vaultrix' );
                    }
            
                    // Don't allow admins to change their email address.
                    if ( current_user_can( 'manage_options' ) ) {
                        $user->user_email = $current_user->user_email;
                    }
            
                    // Validate email address.
                    if ( empty( $user->user_email ) ) {
                        $errors[] = __( 'Please enter an email address.', 'vaultrix' );
                    } elseif ( ! is_email( $user->user_email ) ) {
                        $errors[] = __( 'The email address isn&#8217;t correct.', 'vaultrix' );
                    } else {
                        $owner_id = email_exists( $user->user_email );
                        if ( $owner_id && ( ! $update || ( $owner_id != $user->ID ) ) ) {
                            $errors[] = __( 'This email is already registered, please choose another one.', 'vaultrix' );
                        }
                    }
            
                    /**
                     * Fires before member profile update errors are returned.
                     *
                     * @param $errors WP_Error object (passed by reference).
                     * @param $update Whether this is a user update.
                     * @param $user   User object (passed by reference).
                     */
                    do_action_ref_array( 'pmpro_user_profile_update_errors', array( &$errors, $update, &$user ) );
            
                    // Show error messages.
                    if ( ! empty( $errors ) ) { ?>
                        <div role="alert" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_error', 'pmpro_error' ); ?>">
                            <?php
                                foreach ( $errors as $key => $value ) {
                                    echo '<p>' . $value . '</p>';
                                }
                            ?>
                        </div>
                    <?php } else {
                        // Save updated profile fields.
                        wp_update_user( $user );
                        ?>
                        <div role="alert" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_success', 'pmpro_success' ); ?>">
                            <?php _e( 'Your profile has been updated.', 'vaultrix' ); ?>
                        </div>
                    <?php }
                } else {
                    // Doing this so fields are set to new values after being submitted.
                    $user = $current_user;
                }
                ?>
                <div class="<?php echo pmpro_get_element_class( 'pmpro_member_profile_edit_wrap' ); ?>">
                    <div class="dp-profile-wrap">
                        <h2><?php esc_html_e( 'Profile Picture', 'vaultrix' ); ?></h2>
                        <form id="avatar-upload-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                            <label for="avatar-upload-field" class="screen-reader-text"><?php esc_html_e( 'Avatar Upload Field', 'vaultrix' ); ?></label>
                            <input type="file" name="avatar" accept="image/*" class="btn-main btn-transparent" id="avatar-upload-field" required>
                            <input type="hidden" name="action" value="handle_avatar_upload">
                            <?php wp_nonce_field('avatar-upload-nonce', 'avatar-upload-nonce'); ?>
                            <?php
                                $custom_avatar_url = get_user_meta($current_user->ID, 'custom_avatar_url', true);
                                if ($custom_avatar_url) {
                                    echo '<input type="submit" class="profile-submit" value="Change Photo">';
                                } else {
                                    echo '<input type="submit" class="profile-submit" value="Upload Photo">';
                                }
                            ?>
                        </form>
                    </div>
                    <div class="dp-personal-info-wrap">
                        <h2><?php esc_html_e( 'Personal Information', 'vaultrix' ); ?></h2>
                        <form id="member-profile-edit" class="<?php echo pmpro_get_element_class( 'pmpro_form' ); ?>" action="" method="post"
                            <?php
                                /**
                                 * Fires inside the member-profile-edit form tag in the pmpro_member_profile_edit_form function.
                                 *
                                 * @since 2.4.1
                                 */
                                do_action( 'pmpro_member_profile_edit_form_tag' );
                            ?>
                        >
                
                            <?php wp_nonce_field( 'update-user_' . $current_user->ID, 'update_user_nonce' ); ?>
                
                            <?php
                            $user_fields = apply_filters( 'pmpro_member_profile_edit_user_object_fields',
                                array(
                                    'first_name'	=> __( 'First Name', 'vaultrix' ),
                                    'last_name'		=> __( 'Last Name', 'vaultrix' ),
                                    'display_name'	=> __( 'Display name publicly as', 'vaultrix' ),
                                    'user_email'	=> __( 'Email', 'vaultrix' ),
                                )
                            );
                            ?>
                
                            <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout_box-user' ); ?>">
                                <div class="<?php echo pmpro_get_element_class( 'pmpro_member_profile_edit-fields' ); ?>">
                                <?php foreach ( $user_fields as $field_key => $label ) { ?>
                                    <div class="<?php echo pmpro_get_element_class( 'pmpro_member_profile_edit-field pmpro_member_profile_edit-field- ' . $field_key, 'pmpro_member_profile_edit-field- ' . $field_key ); ?>">
                                        <label for="<?php echo esc_attr( $field_key ); ?>"><?php esc_html_e( $label ); ?></label>
                                        <?php if ( current_user_can( 'manage_options' ) && $field_key === 'user_email' ) { ?>
                                            <input type="text" readonly="readonly" name="user_email" id="user_email" value="<?php echo esc_attr( $user->user_email ); ?>" class="<?php echo pmpro_get_element_class( 'input', 'user_email' ); ?>" />
                                            <p class="<?php echo pmpro_get_element_class( 'lite' ); ?>"><?php esc_html_e( 'Site administrators must use the WordPress dashboard to update their email address.', 'vaultrix' ); ?></p>
                                        <?php } else { ?>
                                            <input type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( stripslashes( $user->{$field_key} ) ); ?>" class="<?php echo pmpro_get_element_class( 'input', $field_key ); ?>" />
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                </div> <!-- end pmpro_member_profile_edit-fields -->
                            </div> <!-- end pmpro_checkout_box-user -->
                
                            <?php
                                /**
                                 * Fires after the default Your Member Profile fields.
                                 *
                                 * @since 2.3
                                 *
                                 * @param WP_User $current_user The current WP_User object.
                                 */
                                do_action( 'pmpro_show_user_profile', $current_user );
                            ?>
                            <input type="hidden" name="action" value="update-profile" />
                            <input type="hidden" name="user_id" value="<?php echo esc_attr( $current_user->ID ) ; ?>" />
                            <div class="<?php echo pmpro_get_element_class( 'pmpro_submit' ); ?>">
                                <input type="submit" name="submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit btn-main', 'pmpro_btn-submit' ); ?>" value="<?php esc_attr_e( 'Update Profile', 'vaultrix' );?>" />
                                <input type="button" name="cancel" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-cancel btn-main btn-transparent', 'pmpro_btn-cancel' ); ?>" value="<?php esc_attr_e( 'Cancel', 'vaultrix' );?>" onclick="location.href='<?php echo pmpro_url( 'account'); ?>';" />
                            </div>
                        </form>
                    </div>
                </div> <!-- end pmpro_member_profile_edit_wrap -->
                <?php
            }
            ?>
            <?php echo sprintf( '<a id="pmpro_actionlink-logout"  class="btn-main btn-transparent dp-mobile-logout" href="%s">%s</a>', esc_url( wp_logout_url() ), esc_html__( 'Log Out', 'vaultrix' ) ); ?>
        </div>
    </div>
</div>
