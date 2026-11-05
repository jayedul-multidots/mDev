<?php
/**
 * Bootstraps the Theme.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Vaultrix
 */
class Vaultrix {
	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		/**
		 * Load classes.
		 */
		Assets::get_instance();
		Menus::get_instance();
		Sidebars::get_instance();
		Register_Post_Types::get_instance();
		Register_Taxonomies::get_instance();
		Blocks::get_instance();
		Register_Shortcodes::get_instance();
		Playlist::get_instance();
		Clone_Functionality::get_instance();
		/**
		 * Load Jetpack compatibility file.
		 */
		if ( defined( 'JETPACK__VERSION' ) ) {
			Theme_Jetpack::get_instance();
		}

		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'wp_head', array( $this, 'pingback_header' ) );

	}

	/**
	 * Setup theme.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function setup_theme() {

		/**
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		/**
		 * Set up the WordPress core custom background feature.
		 */
		add_theme_support(
			'custom-background',
			apply_filters(
				'vaultrix_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		/**
		 * Add theme support for selective refresh for widgets.
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		// Gutenberg theme support.

		/**
		 * Some blocks in Gutenberg like tables, quotes, separator benefit from structural styles (margin, padding, border etc…)
		 * They are applied visually only in the editor (back-end) but not on the front-end to avoid the risk of conflicts with the styles wanted in the theme.
		 * If you want to display them on front to have a base to work with, in this case, you can add support for wp-block-styles, as done below.
		 *
		 * @see Theme Styles.
		 * @link https://make.wordpress.org/core/2018/06/05/whats-new-in-gutenberg-5th-june/, https://developer.wordpress.org/block-editor/developers/themes/theme-support/#default-block-styles
		 */
		add_theme_support( 'wp-block-styles' );

		/**
		 * Some blocks such as the image block have the possibility to define
		 * a “wide” or “full” alignment by adding the corresponding classname
		 * to the block’s wrapper ( alignwide or alignfull ). A theme can opt-in for this feature by calling
		 * add_theme_support( 'align-wide' ), like we have done below.
		 *
		 * @see Wide Alignment
		 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#wide-alignment
		 */
		add_theme_support( 'align-wide' );

		/**
		 * Loads the editor styles in the Gutenberg editor.
		 *
		 * Editor Styles allow you to provide the CSS used by WordPress’ Visual Editor so that it can match the frontend styling.
		 * If we don't add this, the editor styles will only load in the classic editor ( tiny mice )
		 *
		 * @see https://developer.wordpress.org/block-editor/developers/themes/theme-support/#editor-styles
		 */
		add_theme_support( 'editor-styles' );
		/**
		 *
		 * Path to our custom editor style.
		 * It allows you to link a custom stylesheet file to the TinyMCE editor within the post edit screen.
		 *
		 * Since we are not passing any parameter to the function,
		 * it will by default, link the editor-style.css file located directly under the current theme directory.
		 * In our case since we are passing 'assets/build/css/editor.css' path it will use that.
		 * You can change the name of the file or path and replace the path here.
		 *
		 * @see add_editor_style(
		 * @link https://developer.wordpress.org/reference/functions/add_editor_style/
		 */
		add_editor_style( 'assets/build/css/editor.css' );

		/**
		 * Set the maximum allowed width for any content in the theme,
		 * like oEmbeds and images added to posts
		 *
		 * @see Content Width
		 * @link https://codex.wordpress.org/Content_Width
		 */
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1240;
		}
	}

	/**
	 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pingback_header() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}


}
