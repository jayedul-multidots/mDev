<?php
/**
 * Theme setup for mdev FSE.
 *
 * Presentation only: anything that must survive a theme switch belongs in a
 * plugin (e.g. mdev-gb).
 *
 * @package Mdev\Fse
 */

declare( strict_types=1 );

namespace Mdev\Fse;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the theme stylesheet, versioned by the theme version.
 *
 * @since 0.1.0
 *
 * @return void
 */
function enqueue_styles(): void {
	wp_enqueue_style(
		'mdev-fse-style',
		get_stylesheet_uri(),
		[],
		(string) wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_styles' );

/**
 * Load the theme stylesheet in the editor canvas.
 *
 * @since 0.1.0
 *
 * @return void
 */
function editor_styles(): void {
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\editor_styles' );

/**
 * Register the theme pattern category.
 *
 * @since 0.1.0
 *
 * @return void
 */
function register_pattern_category(): void {
	register_block_pattern_category(
		'mdev-fse',
		[ 'label' => __( 'mdev FSE', 'mdev-fse' ) ]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_pattern_category' );

/**
 * Use "Sign in" / "Sign out" wording on the Login/out block in the header.
 *
 * The block prints core's "Log in" / "Log out" strings; they are matched in
 * their translated form, so this works in every site language.
 *
 * @since 0.2.0
 *
 * @param string $block_content Rendered block HTML.
 * @return string
 */
function loginout_labels( string $block_content ): string {
	return strtr(
		$block_content,
		[
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- must match core's own string to replace it.
			'>' . __( 'Log in' ) . '</a>'  => '>' . esc_html__( 'Sign in', 'mdev-fse' ) . '</a>',
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- must match core's own string to replace it.
			'>' . __( 'Log out' ) . '</a>' => '>' . esc_html__( 'Sign out', 'mdev-fse' ) . '</a>',
		]
	);
}
add_filter( 'render_block_core/loginout', __NAMESPACE__ . '\\loginout_labels' );
