<?php
/**
 * Theme Sidebars.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Sidebars
 */
class Sidebars {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {
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
		 * Actions
		 */
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

	}

	/**
	 * Register widgets.
	 *
	 * @action widgets_init
	 * @return void
	 * @since 1.0.0
	 */
	public function register_sidebars() {

		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar', 'vaultrix' ),
				'id'            => 'sidebar-1',
				'description'   => esc_html__( 'Add widgets here.', 'vaultrix' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'           => esc_html__( 'Footer Sidebar 1', 'vaultrix' ),
				'id'             => 'footer-sidebar-1',
				'description'    => esc_html__( 'Add widgets here.', 'vaultrix' ),
				'before_sidebar' => '',
				'after_sidebar'  => '',
				'before_widget'  => '<div id="%1$s" class="widget %2$s">',
				'after_widget'   => '</div>',
				'before_title'   => '<h2 class="widget-title">',
				'after_title'    => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'           => esc_html__( 'Footer Sidebar 2', 'vaultrix' ),
				'id'             => 'footer-sidebar-2',
				'description'    => esc_html__( 'Add widgets here.', 'vaultrix' ),
				'before_sidebar' => '',
				'after_sidebar'  => '',
				'before_widget'  => '<div id="%1$s" class="widget %2$s">',
				'after_widget'   => '</div>',
				'before_title'   => '<h2 class="widget-title">',
				'after_title'    => '</h2>',
			)
		);

	}

}
