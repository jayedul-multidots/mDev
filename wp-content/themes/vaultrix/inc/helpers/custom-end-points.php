<?php
/**
 * Register custom API endpoints
 *
 * @package vaultrix
 */

// register api endpoints for Vaultrix site
add_action( 'rest_api_init', 'vaultrix_register_api_endpoints' );

/**
 * Register custom api endpoints to fetch posts data.
 *
 * @since 1.0.0
 */
function vaultrix_register_api_endpoints() {

	register_rest_route(
		'dp_api',
		'/request/all_videos',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_all_videos_with_params',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'dp_api',
		'/request/video_categories',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_video_categories',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'dp_api',
		'/request/show_categories',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_show_categories',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'dp_api',
		'/request/video_sub_categories',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_video_subcategories_with_param',
			'permission_callback' => '__return_true',
		)
	);

	// Rest API route to get latest series episodes.
	register_rest_route(
		'dp_api',
		'/request/get_latest_series_episodes',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_latest_series_episodes',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'dp_api',
		'/request/series_categories',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_series_categories_with_param',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'dp_api',
		'/request/series_episodes',
		array(
			'methods'             => 'GET',
			'callback'            => 'dp_get_all_videos_with_series_name',
			'permission_callback' => '__return_true',
		)
	);
}

/**
 * Get trending videos list
 */
function dp_get_latest_series_episodes() {
	$result = array();

	$query_args = array(
		'post_type'      => 'deep_pockets_videos',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$query = new \WP_Query( $query_args );

	if ( $query->have_posts() ) {

		$i = 0;

		while ( $query->have_posts() ) {

			$query->the_post();

			$block_post_id = get_the_ID();
			$block_title   = get_the_title();

			$result[ $i ]['id']    = $block_post_id;
			$result[ $i ]['title'] = $block_title;

			$i++;
		}
	}

	return new \WP_REST_Response( $result, 200 );
}

/**
 * Get all resources categories
 */
function dp_get_video_categories() {
	$result = array();

	$resource_categories = get_terms(
		array(
			'taxonomy'   => 'video-show-category',
			'hide_empty' => false,
			'parent'     => 0,
		)
	);

	foreach ( $resource_categories as $resource_category ) {
		$result[] = array(
			'name' => $resource_category->name,
			'slug' => $resource_category->slug,
		);
	}

	return new \WP_REST_Response( $result, 200 );
}

/**
 * Get all resources categories
 */
function dp_get_show_categories() {
	$result = array();

	$child_cats_array    = array();
	$resource_categories = get_terms(
		array(
			'taxonomy'   => 'video-show-category',
			'hide_empty' => false,
			'parent'     => 0,
		)
	);

	foreach ( $resource_categories as $resource_category ) {
		$result[]         = array(
			'name' => $resource_category->name,
			'slug' => $resource_category->slug,
		);
		$child_categories = get_terms(
			array(
				'taxonomy'   => 'video-show-category',
				'hide_empty' => false,
				'parent'     => $resource_category->term_id,
			)
		);
		foreach ( $child_categories as $child_category ) {
			$result[] = array(
				'name' => $child_category->name,
				'slug' => $child_category->slug,
			);
		}
	}

	return new \WP_REST_Response( $result, 200 );
}

/**
 * List of videos by categories.
 *
 * @param array $args.
 */
function dp_query_of_all_videos_by_taxonomy( $args ) {
	$query_args = array(
		'post_type'      => 'deep_pockets_videos',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	if ( ! empty( $args['video_show_category'] ) && empty( $args['video_sub_category'] ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore
			array(
				'taxonomy' => 'video-show-category',
				'field'    => 'slug',
				'terms'    => $args['video_show_category'],
			),
		);
	}

	if ( ! empty( $args['video_show_category'] ) && ! empty( $args['video_sub_category'] ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore
			'relation' => 'AND',
			array(
				'taxonomy' => 'video-show-category',
				'field'    => 'slug',
				'terms'    => $args['video_show_category'],
			),
			array(
				'taxonomy' => 'video-show-category',
				'field'    => 'slug',
				'terms'    => $args['video_sub_category'],
			),
		);
	}
	return dp_fetch_videos_by_taxonomy( $query_args );
}

/**
 * List of posts by cat term.
 *
 * @param array $args arguments.
 */
function dp_fetch_videos_by_taxonomy( $args ) {
	$result = array();

	$query = new \WP_Query( $args );

	if ( $query->have_posts() ) {

		$i = 0;

		while ( $query->have_posts() ) {

			$query->the_post();

			$block_post_id = get_the_ID();
			$block_title   = get_the_title();

			$result[ $i ]['id']    = $block_post_id;
			$result[ $i ]['title'] = $block_title;

			$i++;
		}
	}

	return new \WP_REST_Response( $result, 200 );
}

/**
 * Get videos with params.
 *
 * @param \WP_REST_Request $request reset request.
 */
function dp_get_all_videos_with_params( \WP_REST_Request $request ) {
	$arg                        = array();
	$arg['video_show_category'] = $request->get_param( 'video_show_category' );
	$arg['video_sub_category']  = $request->get_param( 'video_sub_category' );
	return dp_query_of_all_videos_by_taxonomy( $arg );
}

/**
 * List of sub categories by category.
 *
 * @param array $args.
 */
function dp_query_of_sub_categories_by_taxonomy( $args ) {
	$parent_category = get_term_by( 'slug', $args['video_show_category'], 'video-show-category' );

	$result = array();

	// Check if the parent category term exists.
	if ( $parent_category ) {
		// Get the subcategory terms of the parent category.
		$subcategories = get_terms(
			array(
				'taxonomy' => 'video-show-category', // Use 'category' for default WordPress categories.
				'child_of' => $parent_category->term_id,
			)
		);

		// Loop through and display the subcategory terms.
		if ( ! empty( $subcategories ) ) {
			$i = 0;
			foreach ( $subcategories as $subcategory ) {
				$result[ $i ]['name'] = $subcategory->name;
				$result[ $i ]['slug'] = $subcategory->slug;
				$i++;
			}
		}
	}
	return new \WP_REST_Response( $result, 200 );
}

/**
 * Get video sub categories with params.
 *
 * @param \WP_REST_Request $request reset request.
 */
function dp_get_video_subcategories_with_param( \WP_REST_Request $request ) {
	$arg                        = array();
	$arg['video_show_category'] = $request->get_param( 'video_show_category' );
	return dp_query_of_sub_categories_by_taxonomy( $arg );
}


/**
 * List of series sub categories.
 *
 * @param array $args.
 */
function dp_get_series_categories_with_param() {
	$parent_category = get_term_by( 'slug', 'financial-masterclass', 'video-show-category' );

	$result = array();

	// Check if the parent category term exists.
	if ( $parent_category ) {
		// Get the subcategory terms of the parent category.
		$subcategories = get_terms(
			array(
				'taxonomy' => 'video-show-category', // Use 'category' for default WordPress categories.
				'child_of' => $parent_category->term_id,
			)
		);

		// Loop through and display the subcategory terms.
		if ( ! empty( $subcategories ) ) {
			$i = 0;
			foreach ( $subcategories as $subcategory ) {
				$result[ $i ]['name']    = $subcategory->name;
				$result[ $i ]['slug']    = $subcategory->slug;
				$result[ $i ]['term_id'] = $subcategory->term_id;
				$i++;
			}
		}
	}
	return new \WP_REST_Response( $result, 200 );
}

/**
 * Get videos with params.
 *
 * @param \WP_REST_Request $request reset request.
 */
function dp_get_all_videos_with_series_name( \WP_REST_Request $request ) {
	$arg                        = array();
	$arg['video_show_category'] = $request->get_param( 'series' );

	$query_args = array(
		'post_type'      => 'deep_pockets_videos',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	if ( ! empty( $arg['video_show_category'] ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore
			array(
				'taxonomy' => 'video-show-category',
				'field'    => 'slug',
				'terms'    => $arg['video_show_category'],
			),
		);
	}

	return dp_fetch_videos_by_taxonomy( $query_args );
}
