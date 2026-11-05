<?php
/**
 * Admin ACF Page.
 *
 * @package vaultrix
 */

if ( function_exists( 'acf_add_options_page' ) ) {

	acf_add_options_page(
		array(
			'page_title' => __( 'Vaultrix Settings', 'vaultrix' ),
			'menu_title' => __( 'Theme Settings', 'vaultrix' ),
			'menu_slug'  => 'deep-pockets-settings',
			'capability' => 'manage_options',
			'redirect'   => false,
			'position'   => '40',
		)
	);

}

/**
 * Save updated acf-json path.
 *
 * @param string $path acf-json path.
 * @return $path return updated path.
 *
 * @since 1.0.0
 */
function vaultrix_acf_json_save_point( $path ) {
	// update path.
	$path = get_stylesheet_directory() . '/inc/acf-json';

	// return.
	return $path;
}

add_filter( 'acf/settings/save_json', 'vaultrix_acf_json_save_point' );

/**
 * Load updated acf-json path.
 *
 * @param string $paths acf-json path.
 * @return $paths return updated path.
 *
 * @since 1.0.0
 */
function vaultrix_acf_json_load_point( $paths ) {

	// remove original path (optional).
	unset( $paths[0] );

	// append path.
	$paths[] = get_stylesheet_directory() . '/inc/acf-json';

	// return.
	return $paths;
}

add_filter( 'acf/settings/load_json', 'vaultrix_acf_json_load_point' );
