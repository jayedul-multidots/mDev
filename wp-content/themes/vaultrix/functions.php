<?php
/**
 * Theme Functions.
 *
 * @package vaultrix
 */

if ( ! defined( 'VAULTRIX_THEME_VERSION' ) ) {
	define( 'VAULTRIX_THEME_VERSION', '1.0' );
}

if ( ! defined( 'VAULTRIX_THEME_PATH' ) ) {
	define( 'VAULTRIX_THEME_PATH', __DIR__ );
}

if ( ! defined( 'VAULTRIX_THEME_URL' ) ) {
	define( 'VAULTRIX_THEME_URL', get_template_directory_uri() );
}

if ( ! defined( 'VAULTRIX_BUILD_URI' ) ) {
	define( 'VAULTRIX_BUILD_URI', untrailingslashit( get_template_directory_uri() ) . '/assets/build' );
}

if ( ! defined( 'VAULTRIX_BUILD_PATH' ) ) {
	define( 'VAULTRIX_BUILD_PATH', untrailingslashit( get_template_directory() ) . '/assets/build' );
}

if ( ! defined( 'VAULTRIX_SRC_BLOCK_DIR_PATH' ) ) {
	define( 'VAULTRIX_SRC_BLOCK_DIR_PATH', get_template_directory() . '/assets/build/js/blocks' );
}

if ( ! defined( 'VAULTRIX_IMAGES_DIR_PATH' ) ) {
	define( 'VAULTRIX_IMAGES_DIR_PATH', untrailingslashit( get_template_directory_uri() ) . '/assets/src/images/' );
}

if ( ! defined( 'VAULTRIX_MEGAMENU_POST_TYPE' ) ) {
	define( 'VAULTRIX_MEGAMENU_POST_TYPE', 'deeppockets_megamenu' );
}

if ( ! defined( 'VAULTRIX_FAQS_POST_TYPE' ) ) {
	define( 'VAULTRIX_FAQS_POST_TYPE', 'deep_pockets_faqs' );
}

if ( ! defined( 'VAULTRIX_LIBRARY_URI' ) ) {
	define( 'VAULTRIX_LIBRARY_URI', untrailingslashit( get_template_directory_uri() ) . '/assets/library' );
}

if ( ! defined( 'VAULTRIX_LIBRARY_DIR_PATH' ) ) {
	define( 'VAULTRIX_LIBRARY_DIR_PATH', untrailingslashit( get_template_directory() ) . '/assets/library' );
}

if ( ! defined( 'VAULTRIX_VIDEOS_POST_TYPE' ) ) {
	define( 'VAULTRIX_VIDEOS_POST_TYPE', 'deep_pockets_videos' );
}

if ( ! defined( 'VAULTRIX_CONTACT_FORM_ENTRY_POST_TYPE' ) ) {
	define( 'VAULTRIX_CONTACT_FORM_ENTRY_POST_TYPE', 'deep_pockets_contact' );
}

if ( ! defined( 'VAULTRIX_MEMBERS_ENTRY_POST_TYPE' ) ) {
	define( 'VAULTRIX_MEMBERS_ENTRY_POST_TYPE', 'deep_pockets_members' );
}

$google_recaptcha_secret_key = ! empty( get_field( 'google_recaptcha_secret_key', 'option' ) ) ? get_field( 'google_recaptcha_secret_key', 'option' ) : '6LfpFBgpAAAAAGR6eLeBl_z3M9yrctsc7WgmjAaz';
if ( ! defined( 'VAULTRIX_RECAPTCHA_V3_SECRET_KEY' ) ) {
	define( 'VAULTRIX_RECAPTCHA_V3_SECRET_KEY', $google_recaptcha_secret_key );
}

/**
 * Load up the class autoloader.
 */
require_once VAULTRIX_THEME_PATH . '/inc/helpers/autoloader.php';

/**
 * Theme ACF field file.
 */
require_once VAULTRIX_THEME_PATH . '/inc/theme-acf-fields.php';

/**
 * Register all custom API endpoints.
 */
require_once VAULTRIX_THEME_PATH . '/inc/helpers/custom-end-points.php';

/**
 * Functions for video post type.
 */
require_once VAULTRIX_THEME_PATH . '/inc/helpers/global-video-functions.php';


/**
 * Extra Functions file.
 */
require_once VAULTRIX_THEME_PATH . '/inc/helpers/extra-functions.php';

/**
 * Reset password email template.
 */
require_once VAULTRIX_THEME_PATH . '/inc/helpers/reset-password-email.php';

/**
 * Theme Init
 *
 * Sets up the theme.
 *
 * @return void
 * @since 1.0.0
 */
function vaultrix_get_theme_instance() {
	\VAULTRIX\Inc\Vaultrix::get_instance();
}

vaultrix_get_theme_instance();


