<?php
/**
 * Register Post Types
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class for register post types.
 */
class Register_Post_Types {
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
		add_action( 'init', array( $this, 'register_cpts' ) );
	}

	/**
	 * Register Custom Post Types.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_cpts() {

		// post type mega menus.
		$labels = array(
			'name'                  => _x( 'Mega Menus', 'Post Type General Name', 'vaultrix' ),
			'singular_name'         => _x( 'Mega Menu', 'Post Type Singular Name', 'vaultrix' ),
			'menu_name'             => _x( 'Mega Menus', 'Admin Menu text', 'vaultrix' ),
			'name_admin_bar'        => _x( 'Mega Menu', 'Add New on Toolbar', 'vaultrix' ),
			'archives'              => __( 'Mega Menu Archives', 'vaultrix' ),
			'attributes'            => __( 'Mega Menu Attributes', 'vaultrix' ),
			'parent_item_colon'     => __( 'Parent Mega Menu:', 'vaultrix' ),
			'all_items'             => __( 'Mega Menus', 'vaultrix' ),
			'add_new_item'          => __( 'Add New Mega Menu', 'vaultrix' ),
			'add_new'               => __( 'Add New', 'vaultrix' ),
			'new_item'              => __( 'New Mega Menu', 'vaultrix' ),
			'edit_item'             => __( 'Edit Mega Menu', 'vaultrix' ),
			'update_item'           => __( 'Update Mega Menu', 'vaultrix' ),
			'view_item'             => __( 'View Mega Menu', 'vaultrix' ),
			'view_items'            => __( 'View Mega Menus', 'vaultrix' ),
			'search_items'          => __( 'Search Mega Menu', 'vaultrix' ),
			'not_found'             => __( 'Not found', 'vaultrix' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'vaultrix' ),
			'featured_image'        => __( 'Featured Image', 'vaultrix' ),
			'set_featured_image'    => __( 'Set featured image', 'vaultrix' ),
			'remove_featured_image' => __( 'Remove featured image', 'vaultrix' ),
			'use_featured_image'    => __( 'Use as featured image', 'vaultrix' ),
			'insert_into_item'      => __( 'Insert into Mega Menu', 'vaultrix' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Mega Menu', 'vaultrix' ),
			'items_list'            => __( 'Mega Menus list', 'vaultrix' ),
			'items_list_navigation' => __( 'Mega Menus list navigation', 'vaultrix' ),
			'filter_items_list'     => __( 'Filter Mega Menus list', 'vaultrix' ),
		);
		$args   = array(
			'label'              => __( 'Mega Menu', 'vaultrix' ),
			'description'        => '',
			'labels'             => $labels,
			'menu_icon'          => 'dashicons-admin-post',
			'supports'           => array(
				'title',
				'editor',
				'revisions',
				'author',
			),
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => 'themes.php',
			'query_var'          => false,
			'menu_position'      => 24,
			'show_in_admin_bar'  => false,
			'show_in_nav_menus'  => false,
			'can_export'         => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'show_in_rest'       => true,
			'publicly_queryable' => false,
			'capability_type'    => 'post',
		);

		register_post_type( VAULTRIX_MEGAMENU_POST_TYPE, $args );

		$faq_labels = array(
			'name'                  => __( 'Faqs', 'vaultrix' ),
			'singular_name'         => __( 'Faq', 'vaultrix' ),
			'menu_name'             => __( 'Faqs', 'vaultrix' ),
			'name_admin_bar'        => __( 'Faqs', 'vaultrix' ),
			'archives'              => __( 'Item Archives', 'vaultrix' ),
			'attributes'            => __( 'Item Attributes', 'vaultrix' ),
			'parent_item_colon'     => __( 'Parent Item:', 'vaultrix' ),
			'all_items'             => __( 'All Faqs', 'vaultrix' ),
			'add_new_item'          => __( 'Add New Faq', 'vaultrix' ),
			'add_new'               => __( 'Add New', 'vaultrix' ),
			'new_item'              => __( 'New Faq', 'vaultrix' ),
			'edit_item'             => __( 'Edit Faq', 'vaultrix' ),
			'update_item'           => __( 'Update Faq', 'vaultrix' ),
			'view_item'             => __( 'View Faq', 'vaultrix' ),
			'view_items'            => __( 'View Faqs', 'vaultrix' ),
			'search_items'          => __( 'Search Item', 'vaultrix' ),
			'not_found'             => __( 'Not found', 'vaultrix' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'vaultrix' ),
			'featured_image'        => __( 'Featured Image', 'vaultrix' ),
			'set_featured_image'    => __( 'Set featured image', 'vaultrix' ),
			'remove_featured_image' => __( 'Remove featured image', 'vaultrix' ),
			'use_featured_image'    => __( 'Use as featured image', 'vaultrix' ),
			'insert_into_item'      => __( 'Insert into item', 'vaultrix' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'vaultrix' ),
			'items_list'            => __( 'Items list', 'vaultrix' ),
			'items_list_navigation' => __( 'Items list navigation', 'vaultrix' ),
			'filter_items_list'     => __( 'Filter items list', 'vaultrix' ),
		);
		$faq_args   = array(
			'label'               => __( 'Faq', 'vaultrix' ),
			'description'         => 'Frequently Asked Questions',
			'labels'              => $faq_labels,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 15,
			'menu_icon'           => 'dashicons-editor-help',
			'supports'            => array(
				'title',
				'editor',
				'revisions',
				'trackbacks',
				'author',
				'excerpt',
				'page-attributes',
				'thumbnail',
				'custom-fields',
			),
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => array( 'slug' => 'faqs' ),
			'capability_type'     => 'page',
		);
		register_post_type( VAULTRIX_FAQS_POST_TYPE, $faq_args );

		$video_labels = array(
			'name'                  => __( 'Videos', 'vaultrix' ),
			'singular_name'         => __( 'Video', 'vaultrix' ),
			'menu_name'             => __( 'Videos', 'vaultrix' ),
			'name_admin_bar'        => __( 'Videos', 'vaultrix' ),
			'archives'              => __( 'Item Archives', 'vaultrix' ),
			'attributes'            => __( 'Item Attributes', 'vaultrix' ),
			'parent_item_colon'     => __( 'Parent Item:', 'vaultrix' ),
			'all_items'             => __( 'All Videos', 'vaultrix' ),
			'add_new_item'          => __( 'Add New Video', 'vaultrix' ),
			'add_new'               => __( 'Add New', 'vaultrix' ),
			'new_item'              => __( 'New Video', 'vaultrix' ),
			'edit_item'             => __( 'Edit Video', 'vaultrix' ),
			'update_item'           => __( 'Update Video', 'vaultrix' ),
			'view_item'             => __( 'View Video', 'vaultrix' ),
			'view_items'            => __( 'View Videos', 'vaultrix' ),
			'search_items'          => __( 'Search Item', 'vaultrix' ),
			'not_found'             => __( 'Not found', 'vaultrix' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'vaultrix' ),
			'featured_image'        => __( 'Featured Image', 'vaultrix' ),
			'set_featured_image'    => __( 'Set featured image', 'vaultrix' ),
			'remove_featured_image' => __( 'Remove featured image', 'vaultrix' ),
			'use_featured_image'    => __( 'Use as featured image', 'vaultrix' ),
			'insert_into_item'      => __( 'Insert into item', 'vaultrix' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'vaultrix' ),
			'items_list'            => __( 'Items list', 'vaultrix' ),
			'items_list_navigation' => __( 'Items list navigation', 'vaultrix' ),
			'filter_items_list'     => __( 'Filter items list', 'vaultrix' ),
		);
		$video_args   = array(
			'label'              => __( 'Video', 'vaultrix' ),
			'description'        => 'Post Type Description',
			'labels'             => $video_labels,
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 15,
			'menu_icon'          => 'dashicons-media-text',
			'supports'           => array(
				'title',
				'editor',
				'revisions',
				'trackbacks',
				'author',
				'excerpt',
				'page-attributes',
				'thumbnail',
				'custom-fields',
			),
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => true,
			'can_export'         => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'rewrite'            => array( 'slug' => 'vaultrix-videos' ),
			'capability_type'    => 'page',
		);
		register_post_type( VAULTRIX_VIDEOS_POST_TYPE, $video_args );

		$contact_form_entries_labels = array(
			'name'                  => __( 'Contact Form Entries', 'vaultrix' ),
			'singular_name'         => __( 'Contact Form Entries', 'vaultrix' ),
			'menu_name'             => __( 'Contact Form Entries', 'vaultrix' ),
			'name_admin_bar'        => __( 'Contact Form Entries', 'vaultrix' ),
			'archives'              => __( 'Item Archives', 'vaultrix' ),
			'attributes'            => __( 'Item Attributes', 'vaultrix' ),
			'parent_item_colon'     => __( 'Parent Item:', 'vaultrix' ),
			'all_items'             => __( 'All Form Entries', 'vaultrix' ),
			'add_new_item'          => __( 'Add New Entry', 'vaultrix' ),
			'add_new'               => __( 'Add New', 'vaultrix' ),
			'new_item'              => __( 'New Form Entries', 'vaultrix' ),
			'edit_item'             => __( 'Edit Entry', 'vaultrix' ),
			'update_item'           => __( 'Update Entry', 'vaultrix' ),
			'view_item'             => __( 'View Entry', 'vaultrix' ),
			'view_items'            => __( 'View Entries', 'vaultrix' ),
			'search_items'          => __( 'Search Item', 'vaultrix' ),
			'not_found'             => __( 'Not found', 'vaultrix' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'vaultrix' ),
			'featured_image'        => __( 'Featured Image', 'vaultrix' ),
			'set_featured_image'    => __( 'Set featured image', 'vaultrix' ),
			'remove_featured_image' => __( 'Remove featured image', 'vaultrix' ),
			'use_featured_image'    => __( 'Use as featured image', 'vaultrix' ),
			'insert_into_item'      => __( 'Insert into item', 'vaultrix' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'vaultrix' ),
			'items_list'            => __( 'Items list', 'vaultrix' ),
			'items_list_navigation' => __( 'Items list navigation', 'vaultrix' ),
			'filter_items_list'     => __( 'Filter items list', 'vaultrix' ),
		);
		$contact_form_entries_args   = array(
			'label'              => __( 'Contact Form Entries', 'vaultrix' ),
			'description'        => 'Post Type Description',
			'labels'             => $contact_form_entries_labels,
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 15,
			'menu_icon'          => 'dashicons-email-alt',
			'supports'           => array(
				'title',
				'editor',
				'revisions',
				'trackbacks',
				'author',
				'excerpt',
				'page-attributes',
				'thumbnail',
				'custom-fields',
			),
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => true,
			'can_export'         => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'rewrite'            => true,
			'capability_type'    => 'page',
		);
		register_post_type( VAULTRIX_CONTACT_FORM_ENTRY_POST_TYPE, $contact_form_entries_args );

		$members_entries_labels = array(
			'name'                  => __( 'Members Entries', 'vaultrix' ),
			'singular_name'         => __( 'Members Entries', 'vaultrix' ),
			'menu_name'             => __( 'Members Entries', 'vaultrix' ),
			'name_admin_bar'        => __( 'Members Entries', 'vaultrix' ),
			'archives'              => __( 'Item Archives', 'vaultrix' ),
			'attributes'            => __( 'Item Attributes', 'vaultrix' ),
			'parent_item_colon'     => __( 'Parent Item:', 'vaultrix' ),
			'all_items'             => __( 'All Form Entries', 'vaultrix' ),
			'add_new_item'          => __( 'Add New Entry', 'vaultrix' ),
			'add_new'               => __( 'Add New', 'vaultrix' ),
			'new_item'              => __( 'New Form Entries', 'vaultrix' ),
			'edit_item'             => __( 'Edit Entry', 'vaultrix' ),
			'update_item'           => __( 'Update Entry', 'vaultrix' ),
			'view_item'             => __( 'View Entry', 'vaultrix' ),
			'view_items'            => __( 'View Entries', 'vaultrix' ),
			'search_items'          => __( 'Search Item', 'vaultrix' ),
			'not_found'             => __( 'Not found', 'vaultrix' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'vaultrix' ),
			'featured_image'        => __( 'Featured Image', 'vaultrix' ),
			'set_featured_image'    => __( 'Set featured image', 'vaultrix' ),
			'remove_featured_image' => __( 'Remove featured image', 'vaultrix' ),
			'use_featured_image'    => __( 'Use as featured image', 'vaultrix' ),
			'insert_into_item'      => __( 'Insert into item', 'vaultrix' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'vaultrix' ),
			'items_list'            => __( 'Items list', 'vaultrix' ),
			'items_list_navigation' => __( 'Items list navigation', 'vaultrix' ),
			'filter_items_list'     => __( 'Filter items list', 'vaultrix' ),
		);
		$members_entries_args   = array(
			'label'              => __( 'Members Entries', 'vaultrix' ),
			'description'        => 'Post Type Description',
			'labels'             => $members_entries_labels,
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 15,
			'menu_icon'          => 'dashicons-admin-users',
			'supports'           => array(
				'title',
				'editor',
				'revisions',
				'trackbacks',
				'author',
				'excerpt',
				'page-attributes',
				'thumbnail',
				'custom-fields',
			),
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => true,
			'can_export'         => true,
			'has_archive'        => false,
			'publicly_queryable' => false,
			'rewrite'            => true,
			'capability_type'    => 'page',
		);
		register_post_type( VAULTRIX_MEMBERS_ENTRY_POST_TYPE, $members_entries_args );
	}
}
