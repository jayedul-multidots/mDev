<?php
/**
 * Theme setup for mdev Block.
 *
 * Presentation only: anything that must survive a theme switch belongs in a
 * plugin (e.g. mdev-gb).
 *
 * @package Mdev\Block
 */

declare( strict_types=1 );

namespace Mdev\Block;

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
		'mdev-block-style',
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
		'mdev-block',
		[ 'label' => __( 'mdev Block', 'mdev-block' ) ]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_pattern_category' );
