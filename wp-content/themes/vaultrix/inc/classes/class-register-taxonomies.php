<?php
/**
 * Register Custom Taxonomies
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class for register taxonomies.
 */
class Register_Taxonomies {
	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// load class.
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
		add_action( 'init', array( $this, 'register_taxonomy' ) );

	}

	/**
	 * Register Taxonomy Year.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_taxonomy() {

		$videos_categories_labels = array(
			'name'              => _x( 'Shows', 'Show', 'vaultrix' ),
			'singular_name'     => _x( 'Show', 'Show', 'vaultrix' ),
			'search_items'      => __( 'Search Shows', 'vaultrix' ),
			'all_items'         => __( 'All Shows', 'vaultrix' ),
			'parent_item'       => __( 'Parent Show', 'vaultrix' ),
			'parent_item_colon' => __( 'Parent Show:', 'vaultrix' ),
			'edit_item'         => __( 'Edit Show', 'vaultrix' ),
			'update_item'       => __( 'Update Show', 'vaultrix' ),
			'add_new_item'      => __( 'Add New Show', 'vaultrix' ),
			'new_item_name'     => __( 'New Show Name', 'vaultrix' ),
			'menu_name'         => __( 'Shows', 'vaultrix' ),
		);

		$videos_categories_args = array(
			'public'            => true,
			'hierarchical'      => true,
			'labels'            => $videos_categories_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array(
				'slug' => 'shows',
			),
		);

		register_taxonomy( 'video-show-category', array( 'deep_pockets_videos' ), $videos_categories_args );

		$season_categories_labels = array(
			'name'              => _x( 'Seasons', 'Season', 'vaultrix' ),
			'singular_name'     => _x( 'Season', 'Season', 'vaultrix' ),
			'search_items'      => __( 'Search Seasons', 'vaultrix' ),
			'all_items'         => __( 'All Seasons', 'vaultrix' ),
			'parent_item'       => __( 'Parent Season', 'vaultrix' ),
			'parent_item_colon' => __( 'Parent Season:', 'vaultrix' ),
			'edit_item'         => __( 'Edit Season', 'vaultrix' ),
			'update_item'       => __( 'Update Season', 'vaultrix' ),
			'add_new_item'      => __( 'Add New Season', 'vaultrix' ),
			'new_item_name'     => __( 'New Season Name', 'vaultrix' ),
			'menu_name'         => __( 'Seasons', 'vaultrix' ),
		);

		$season_categories_args = array(
			'public'            => true,
			'hierarchical'      => true,
			'labels'            => $season_categories_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array(
				'slug' => 'seasons',
			),
		);

		register_taxonomy( 'video-season-category', array( 'deep_pockets_videos' ), $season_categories_args );
	}

}
