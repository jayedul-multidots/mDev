<?php
/**
 * Vaultrix Post Clone
 *
 * @since 1.0.0
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Clone Functionality Class.
 *
 * @since 1.0.0
 */
class Clone_Functionality {
	use Singleton;

	/**
	 * Allowed Post Types
	 *
	 * @var array
	 */
	public $allowed_post_types = array();


	/**
	 * Class Constructor
	 */
	protected function __construct() {

		$this->allowed_post_types = array(
			'post',
			'page',
			VAULTRIX_FAQS_POST_TYPE,
			VAULTRIX_VIDEOS_POST_TYPE,
		);

		$this->setup_hooks();
	}


	/**
	 * Hooks for Post Preview.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {

		add_filter( 'post_row_actions', array( $this, 'vaultrix_clone_post_actions' ), 10, 2 );

		add_filter( 'page_row_actions', array( $this, 'vaultrix_clone_post_actions' ), 10, 2 );

		add_action( 'admin_action_vaultrix_clone_post_as_new', array( $this, 'vaultrix_clone_post_as_new' ) );

		add_action( 'admin_notices', array( $this, 'vaultrix_post_clone_action_admin_notice' ) );

		add_action( 'vaultrix_clone_post', array( $this, 'vaultrix_clone_post_save_meta' ), 10, 2 );

		add_action( 'vaultrix_clone_post', array( $this, 'vaultrix_clone_post_save_taxonomies' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'vaultrix_add_clone_metabox' ) );

		add_filter( 'removable_query_args', array( $this, 'vaultrix_clone_post_add_removable_query_arg' ), 10, 1 );
	}

	/**
	 * Post Clone in Quick Actions
	 *
	 * @param array  $actions actions return the clone link html.
	 * @param object $post posts attribute is to tell which posts we are including.
	 * @return array
	 */
	public function vaultrix_clone_post_actions( $actions, $post ) {

		if ( false !== in_array( $post->post_type, $this->allowed_post_types, true ) ) {

			$action     = '?action=vaultrix_clone_post_as_new&amp;post=' . $post->ID;
			$action_url = wp_nonce_url( admin_url( 'admin.php' . $action ), 'clone-post_' . $post->ID );

			$actions['vaultrix-clone'] = '<a href="' . esc_url( $action_url ) . '" aria-label="Clone">Clone</a>';

		}

		return $actions;

	}

	/**
	 * Clone Post Action Callback
	 */
	public function vaultrix_clone_post_as_new() {

		$post_id  = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$action   = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
		$redirect = filter_input( INPUT_GET, 'redirect', FILTER_SANITIZE_STRING );

		if ( empty( $post_id ) || empty( $action ) || 'vaultrix_clone_post_as_new' !== $action ) {
			wp_die( esc_html__( 'No post to clone has been supplied!', 'vaultrix' ) );
		}

		check_admin_referer( 'clone-post_' . $post_id );

		$post = get_post( $post_id );

		if ( isset( $post ) && null !== $post ) {
			$post_type = $post->post_type;
			$new_id    = $this->vaultrix_create_clone_post( $post );

			if ( isset( $redirect ) && 'post' === $redirect ) {
				wp_safe_redirect(
					add_query_arg(
						array(
							'cloned' => 1,
							'ids'    => $post->ID,
						),
						admin_url( 'post.php?action=edit&post=' . $new_id )
					),
					302,
					'vaultrix'
				);
				exit;
			} else {
				$sendback = wp_get_referer();
				if ( ! $sendback || false !== strpos( $sendback, 'post.php' ) || false !== strpos( $sendback, 'post-new.php' ) ) {
					$sendback = admin_url( 'edit.php' );
					if ( ! empty( $post_type ) ) {
						$sendback = add_query_arg( 'post_type', $post_type, $sendback );
					}
				} else {
					$sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'cloned', 'ids' ), $sendback );
				}

				// Redirect to the post list screen.
				wp_safe_redirect(
					add_query_arg(
						array(
							'cloned' => 1,
							'ids'    => $post->ID,
						),
						$sendback
					),
					302,
					'vaultrix'
				);
				exit;
			}
		} else {
			wp_die( esc_html__( 'Clone failed, could not find original: ' . $post_id, 'vaultrix' ) ); //phpcs:ignore
		}

	}

	/**
	 * Clone post
	 *
	 * @param Object $post here post is the individual post having the post data like title, content, etc.
	 */
	public function vaultrix_create_clone_post( $post ) {

		$new_post = array(
			'post_title'            => ( isset( $post->post_title ) && ! empty( $post->post_title ) ) ? 'Copy of ' . $post->post_title : '',
			'post_type'             => $post->post_type,
			'post_status'           => 'draft', // always make the cloned post as draft.
			'post_author'           => get_current_user_id(),
			'post_password'         => $post->post_password,
			'post_parent'           => $post->post_parent,
			'post_mime_type'        => $post->post_mime_type,
			'post_excerpt'          => $post->post_excerpt,
			'post_content'          => $post->post_content,
			'post_content_filtered' => $post->post_content_filtered,
			'menu_order'            => $post->menu_order,
			'comment_status'        => $post->comment_status,
		);

		// Generate a new unique slug for the cloned post.
		$new_post['post_name'] = wp_unique_post_slug( sanitize_title( $new_post['post_title'] ), $post->ID, $new_post['post_status'], $new_post['post_type'], $post->post_parent );

		$new_post_id = wp_insert_post( wp_slash( $new_post ), true );

		if ( empty( $new_post_id ) || is_wp_error( $new_post_id ) ) {
			wp_die( esc_html__( 'There was an error while cloning.', 'vaultrix' ) );
		}

		do_action( 'vaultrix_clone_post', $new_post_id, $post );

		delete_post_meta( $new_post_id, '_vaultrix_original' );
		add_post_meta( $new_post_id, '_vaultrix_original', $post->ID );

		return $new_post_id;
	}

	/**
	 * Admin Notices for action message
	 */
	public function vaultrix_post_clone_action_admin_notice() {

		$cloned = filter_input( INPUT_GET, 'cloned', FILTER_SANITIZE_NUMBER_INT );
		if ( isset( $cloned ) && ! empty( $cloned ) ) {

			$copied_posts = intval( $cloned );
			echo wp_kses_post(
				sprintf(
					'<div id="message" class="notice notice-success fade"><p>' .
					/* translators: %s: number */
					_n(
						'%s item copied.',
						'%s items copied.',
						$copied_posts,
						'vaultrix'
					) . '</p></div>',
					$copied_posts
				)
			);
			remove_query_arg( 'cloned' );
		}
	}

	/**
	 * Save cloned post meta fields
	 *
	 * @param int    $new_id attrbute new_id will contain the id of new post which is cloned.
	 * @param object $post attrbute post will contain the id of old post.
	 */
	public function vaultrix_clone_post_save_meta( $new_id, $post ) {
		$meta_fields = get_post_meta( $post->ID, '', true );

		if ( empty( $meta_fields ) ) {
			return;
		}

		$not_to_be_cloned = array(
			'_edit_lock',
			'_edit_last',
			'do_you_wvaultrix_to_schedule_changes',
			'post_after',
		);

		foreach ( $meta_fields as $meta_key => $meta_value ) {
			if ( false === in_array( $meta_key, $not_to_be_cloned, true ) ) {
				$meta_value = maybe_unserialize( $meta_value[0] );
				add_post_meta( $new_id, $meta_key, vaultrix_post_wp_slash( $meta_value ) );
			}
		}
	}

	/**
	 * Save cloned post taxonomies
	 *
	 * @param int    $new_id attrbute new_id will contain the id of new post which is cloned.
	 * @param object $post attrbute post will contain the id of old post.
	 */
	public function vaultrix_clone_post_save_taxonomies( $new_id, $post ) {

		// Clear default category (added by wp_insert_post).
		wp_set_object_terms( $new_id, null, 'category' );

		$taxonomies = get_object_taxonomies( $post->post_type );

		if ( post_type_supports( $post->post_type, 'post-formats' ) && ! in_array( 'post_format', $taxonomies, true ) ) {
			$taxonomies[] = 'post_format';
		}

		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'orderby' => 'term_order' ) );
			$terms      = array();
			for ( $i = 0; $i < count( $post_terms ); $i++ ) { //phpcs:ignore
				$terms[] = $post_terms[ $i ]->slug;
			}
			wp_set_object_terms( $new_id, $terms, $taxonomy );
		}
	}

	/**
	 * Clone Meta Box for the edit post screen
	 */
	public function vaultrix_add_clone_metabox() {
		global $post;

		$clone_post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		if ( isset( $clone_post_id ) && ! empty( $clone_post_id ) ) {

			$title = ( isset( $post->post_type ) ) ? 'Clone ' . get_post_type_object( $post->post_type )->labels->singular_name : 'Clone';

			add_meta_box(
				'vaultrix_clone_metabox',
				$title,
				array( $this, 'vaultrix_clone_metabox_callback' ),
				$this->allowed_post_types,
				'side'
			);

		}

	}

	/**
	 * Clone Meta Box Callback
	 */
	public function vaultrix_clone_metabox_callback() {
		global $post;

		$action     = '?action=vaultrix_clone_post_as_new&amp;post=' . $post->ID . '&amp;redirect=post';
		$action_url = wp_nonce_url( admin_url( 'admin.php' . $action ), 'clone-post_' . $post->ID );
		?>
			<div class="vaultrix-clone-post">
				<a class="button button-primary button-large" href="<?php echo esc_url( $action_url ); ?>" aria-label="Clone">Clone</a>
			</div>

			<?php
	}

	/**
	 * Adds custom removable arguments from query string
	 *
	 * @param array $removable_query_args removable_query_args attribute to add the clone removable query.
	 * @return array
	 */
	public function vaultrix_clone_post_add_removable_query_arg( $removable_query_args ) {

		$removable_query_args[] = 'cloned';

		return $removable_query_args;
	}


}
