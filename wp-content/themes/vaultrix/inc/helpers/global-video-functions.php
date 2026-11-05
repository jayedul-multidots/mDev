<?php

/**
 * Get trending articles by views from Jetpack
 *
 * @param integer $days Days to get the trending articles.
 * @param integer $limit Limit of the articles to get.
 * @return array
 */
function dp_trending_series( $days = 30, $limit = 10 ) {
	$counter = 0;
	$types   = array( 'deep_pockets_videos' );
	$posts   = array();

	if ( ! defined( 'VIP_GO_APP_ENVIRONMENT' ) ) {
		return array();
	}

	$post_view_posts = stats_get_from_restapi( array(), 'top-posts?max=11&summarize=1&num=' . intval( $days ) );

	if ( ! isset( $post_view_posts->summary ) || empty( $post_view_posts->summary->postviews ) ) {
		return array();
	}

	$post_view_ids = array_filter( wp_list_pluck( $post_view_posts->summary->postviews, 'id' ) );

	if ( ! $post_view_ids ) {
		return array();
	}

	foreach ( (array) $post_view_ids as $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			continue;
		}

		/**
		 * Attachment pages use the 'inherit' post status by default.
		 * To be able to remove attachment pages from private and password protect posts,
		 * we need to replace their post status by the parent post' status.
		 */
		if ( 'inherit' === $post->post_status && 'attachment' === $post->post_type ) {
			$post->post_status = get_post_status( $post_id );
		}

		// hide private and password protected posts
		if ( 'publish' !== $post->post_status || ! empty( $post->post_password ) ) {
			continue;
		}

		// Filter by chosen Post Types.
		if ( ! in_array( $post->post_type, $types, true ) ) {
			continue;
		}

		// Both get HTML stripped etc on display
		if ( empty( $post->post_title ) ) {
			$title_source = $post->post_content;
			$title        = wp_html_excerpt( $title_source, 50 );
			$title       .= '&hellip;';
		} else {
			$title = $post->post_title;
		}

		$permalink = get_permalink( $post->ID );

		$post_type = $post->post_type;

		$posts[] = compact( 'title', 'permalink', 'post_id', 'post_type' );
		$counter++;

		if ( $counter === (int) $limit ) {
			break; // only need to load and show x number of likes
		}
	}

	return $posts;
}

/**
 * get  recent posts
 *
 * @since    1.0.0
 */
function get_recent_videos( $posts_per_page ) {

	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_type'      => 'deep_pockets_videos',
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	);

	$query = new wp_query( $args );
	if ( $query->post_count > 0 ) {
		return $query->posts;
	}
	wp_reset_postdata();
	return null;

}

/**
 * get trending posts
 *
 * @since    1.0.0
 */
function get_trending_articles( $days = 30 ) {
	$limit = 50;

	$popular_posts = dp_trending_series( $days, $limit );

	if ( is_array( $popular_posts ) && count( $popular_posts ) > 0 ) {

		$result = array();

		foreach ( $popular_posts as $item ) {
			$post = get_post( $item['post_id'] );
			if ( ! empty( $post ) && $post->post_type === 'post' ) {
				$result[] = $post;
			}
		}

		if ( ! empty( $result ) ) {
			$result = ( count( $result ) > $limit ) ? array_slice( $result, 0, $limit ) : $result;
			return $result;
		}
	}
	return get_recent_videos( $limit );
}
