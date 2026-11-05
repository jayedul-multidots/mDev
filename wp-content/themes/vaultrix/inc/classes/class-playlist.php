<?php
/**
 * Enqueue theme assets
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Assets
 */
class Playlist {
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
		add_action( 'wp_ajax_dp_create_video_popup_html', array( $this, 'dp_create_video_popup_html' ) );
		add_action( 'wp_ajax_nopriv_dp_create_video_popup_html', array( $this, 'dp_create_video_popup_html' ) );

		add_action( 'wp_ajax_dp_playlist_popup', array( $this, 'dp_playlist_popup' ) );
		add_action( 'wp_ajax_nopriv_dp_playlist_popup', array( $this, 'dp_playlist_popup' ) );

		add_action( 'wp_ajax_dp_add_playlistname_to_db', array( $this, 'dp_add_playlistname_to_db' ) );
		add_action( 'wp_ajax_nopriv_dp_add_playlistname_to_db', array( $this, 'dp_add_playlistname_to_db' ) );

		add_action( 'wp_ajax_dp_update_playlist', array( $this, 'dp_update_playlist' ) );
		add_action( 'wp_ajax_nopriv_dp_update_playlist', array( $this, 'dp_update_playlist' ) );

		add_action( 'wp_ajax_dp_remove_playlist', array( $this, 'dp_remove_playlist' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_playlist', array( $this, 'dp_remove_playlist' ) );

		add_action( 'wp_ajax_dp_add_item_to_playlist', array( $this, 'dp_add_item_to_playlist' ) );
		add_action( 'wp_ajax_nopriv_dp_add_item_to_playlist', array( $this, 'dp_add_item_to_playlist' ) );

		add_action( 'wp_ajax_dp_remove_item_from_playlist', array( $this, 'dp_remove_item_from_playlist' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_item_from_playlist', array( $this, 'dp_remove_item_from_playlist' ) );

		// Actions for season popup
		add_action( 'wp_ajax_dp_create_series_popup_html', array( $this, 'dp_create_series_popup_html' ) );
		add_action( 'wp_ajax_nopriv_dp_create_series_popup_html', array( $this, 'dp_create_series_popup_html' ) );

		add_action( 'wp_ajax_dp_update_season_list', array( $this, 'dp_update_season_list' ) );
		add_action( 'wp_ajax_nopriv_dp_update_season_list', array( $this, 'dp_update_season_list' ) );

		// Actions for trending now functionality of videos
		add_action( 'wp_ajax_trending_videos_listing', array( $this, 'dp_trending_videos_listing_callback' ) );
		add_action( 'wp_ajax_nopriv_trending_videos_listing', array( $this, 'dp_trending_videos_listing_callback' ) );

		add_action( 'wp_ajax_dp_create_watch_video_html', array( $this, 'dp_create_watch_video_html_callback' ) );
		add_action( 'wp_ajax_nopriv_dp_create_watch_video_html', array( $this, 'dp_create_watch_video_html_callback' ) );

		add_action( 'wp_ajax_dp_create_series_trailer_html', array( $this, 'dp_create_series_trailer_html_callback' ) );
		add_action( 'wp_ajax_nopriv_dp_create_series_trailer_html', array( $this, 'dp_create_series_trailer_html_callback' ) );

		add_action( 'wp_ajax_dp_update_video_action_buttons', array( $this, 'dp_update_video_action_buttons_callback' ) );
		add_action( 'wp_ajax_nopriv_dp_update_video_action_buttons', array( $this, 'dp_update_video_action_buttons_callback' ) );

		add_action( 'wp_ajax_nopriv_brightcove_video_set_time', array( $this, 'dp_brightcove_video_set_time' ) );
		add_action( 'wp_ajax_brightcove_video_set_time', array( $this, 'dp_brightcove_video_set_time' ) );

		add_action( 'wp_ajax_nopriv_brightcove_video_get_time', array( $this, 'dp_brightcove_video_get_time' ) );
		add_action( 'wp_ajax_brightcove_video_get_time', array( $this, 'dp_brightcove_video_get_time' ) );

		add_action( 'wp_ajax_dp_set_video_duration_time', array( $this, 'dp_set_video_duration_time_callback' ) );
		add_action( 'wp_ajax_nopriv_dp_set_video_duration_time', array( $this, 'dp_set_video_duration_time_callback' ) );

		// Player API endpoint register for previous next functionality
		add_action( 'rest_api_init', array( $this, 'dp_player_api_register' ) );

	}

	/**
	 * Callback function to create playlist popup
	 * It works for both video and season popup
	 */
	public function dp_playlist_popup() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$user_id        = get_current_user_id();
			$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
			ob_start();
			?>
			<div id="playlist-popup" class="playlist-popup">
				<span class="playlist-popup-close"></span>
				<div class='popup-head-wrapper'>
					<div class="head-content">
						<div class="heading">Add to your Playlist</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididu.</p>
					</div>
					<div class='popup-playlists'>
						<?php if ( ! empty( $user_playlists ) ) { ?>
							<?php foreach ( $user_playlists as $playlist_name ) { ?>
								<div class="playlist-option">
									<div class="playlist-radio-box">
										<input type='radio' autocomplete='off' name='playlist' value='<?php echo esc_attr( $playlist_name['slug'] ); ?>' id='<?php echo esc_attr( $playlist_name['slug'] ); ?>'/>
										<label for='<?php echo esc_attr( $playlist_name['slug'] ); ?>'><?php echo esc_html( $playlist_name['label'] ); ?></label>
									</div>
									<div class="playlist-remove" data-playlist-slug='<?php echo esc_attr( $playlist_name['slug'] ); ?>'></div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<div class="new-playlist-setting">
					<button class="create-playlist"><i class="fas fa-plus"></i>Create a Playlist</button>
					<div class="playlist-input-form" style="display:none">
						<label for="playlist-name">Name</label>
						<input type="text" name="playlist-name" class="playlist-name" id="playlist-name" placeholder="Create a Name for Your Playlist" autocomplete="off"/>
						<div class="playlist-note" style="display:none"></div>
						<div class="save-playlist-wrapper"><button class="save-playlist">Save Playlist</button></div>
					</div>
				</div>
				<button class="btn-add-to-playlist btn-main">Add to Playlist</button>
				<div class="playlist-text" style="display:none">Please Select the Playlist !!</div>
			</div>
			<?php
		}
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to save newly created playlist
	 * It works for both video and season popup
	 */
	public function dp_add_playlistname_to_db() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$user_id       = get_current_user_id();
			$playlist_name = filter_input( INPUT_POST, 'plname', FILTER_SANITIZE_STRING );
			$data_array    = array();
			if ( $user_id && $playlist_name ) {
				$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
				if ( ! empty( $user_playlists ) ) {
					$playlist_slug = sanitize_title( $playlist_name );
					if ( ! array_key_exists( $playlist_slug, $user_playlists ) ) {
						$user_playlists[ $playlist_slug ] = array(
							'label'  => $playlist_name,
							'slug'   => $playlist_slug,
							'video'  => array(),
							'series' => array(),
						);
						update_user_meta( $user_id, 'user_playlist', $user_playlists );
						echo 'success';
					} else {
						echo 'Playlist already exist !!';
					}
				} else {
					$playlist_slug                = sanitize_title( $playlist_name );
					$data_array[ $playlist_slug ] = array(
						'label'  => $playlist_name,
						'slug'   => $playlist_slug,
						'video'  => array(),
						'series' => array(),
					);
					update_user_meta( $user_id, 'user_playlist', $data_array );
					echo 'success';
				}
			} else {
				echo 'Please add playlist name !!';
			}
		}
		wp_die();
	}

	/**
	 * Callback function to save newly created playlist
	 * It works for both video and season popup
	 */
	public function dp_update_playlist() {
		$user_id        = get_current_user_id();
		$user_playlists = get_user_meta( $user_id, 'user_playlist', true );

		ob_start();
		if ( ! empty( $user_playlists ) ) {
			foreach ( $user_playlists as $playlist_name ) {
				?>
				<div class="playlist-option">
					<div class="playlist-radio-box">
						<input type='radio' name='playlist' value='<?php echo esc_attr( $playlist_name['slug'] ); ?>' id='<?php echo esc_attr( $playlist_name['slug'] ); ?>' />
						<label for='<?php echo esc_attr( $playlist_name['slug'] ); ?>'><?php echo esc_html( $playlist_name['label'] ); ?></label>
					</div>
					<div class="playlist-remove" data-playlist-slug='<?php echo esc_attr( $playlist_name['slug'] ); ?>'></div>
				</div>
				<?php
			}
		}
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to save newly created playlist
	 * It works for both video and season popup
	 */
	public function dp_remove_playlist() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$playlist_slug  = filter_input( INPUT_POST, 'plslug', FILTER_SANITIZE_STRING );
			$user_id        = get_current_user_id();
			$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
			$recently_added = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
			ob_start();
			if ( ! empty( $user_playlists ) && ! empty( $playlist_slug ) ) {
				foreach ( $user_playlists as $key => $value ) {
					if ( $playlist_slug === $key ) {
						$video_array = $user_playlists[ $playlist_slug ]['video'];
						foreach ( $video_array as $video_id ) {
							$index = array_search( $video_id, $recently_added, true );
							if ( $index !== false ) {
								unset( $recently_added[ $index ] );
								$recently_added = array_values( $recently_added );
							}
							update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
						}
						unset( $user_playlists[ $key ] );
						update_user_meta( $user_id, 'user_playlist', $user_playlists );
					}
				}
				foreach ( $user_playlists as $playlist_name ) {
					?>
					<div class="playlist-option">
						<div class="playlist-radio-box">
							<input type='radio' name='playlist' value='<?php echo esc_attr( $playlist_name['slug'] ); ?>' id='<?php echo esc_attr( $playlist_name['slug'] ); ?>' />
							<label for='<?php echo esc_attr( $playlist_name['slug'] ); ?>'><?php echo esc_html( $playlist_name['label'] ); ?></label>
						</div>
						<div class="playlist-remove" data-playlist-slug='<?php echo esc_attr( $playlist_name['slug'] ); ?>'></div>
					</div>
					<?php
				}
			}
			$html = ob_get_clean();
		}
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * 2026-07-01: ACF main_video helpers — replaces Brightcove IDs for native playback and prev/next lists.
	 */
	private function resolve_main_video_source( array $main_video ): array {
		$video_url     = $main_video['url'] ?? '';
		$video_type    = $main_video['mime_type'] ?? '';
		$attachment_id = $main_video['ID'] ?? ( $main_video['id'] ?? 0 );
		$attachment_id = (int) $attachment_id;

		if ( $attachment_id > 0 ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$video_url = $attachment_url;
			}

			if ( empty( $video_type ) ) {
				$video_type = get_post_mime_type( $attachment_id ) ?: '';
			}
		}

		return array(
			'url'           => $video_url,
			'type'          => $video_type,
			'attachment_id' => $attachment_id,
		);
	}

	/**
	 * Resolve the trailer video source URL and mime type from an ACF file field.
	 *
	 * @param array $trailer_video ACF file field array.
	 * @return array{url: string, type: string, attachment_id: int}
	 */
	private function resolve_trailer_video_source( array $trailer_video ): array {
		$video_url     = $trailer_video['url'] ?? '';
		$video_type    = $trailer_video['mime_type'] ?? '';
		$attachment_id = $trailer_video['ID'] ?? ( $trailer_video['id'] ?? 0 );
		$attachment_id = (int) $attachment_id;

		if ( $attachment_id > 0 ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( $attachment_url ) {
				$video_url = $attachment_url;
			}

			if ( empty( $video_type ) ) {
				$video_type = get_post_mime_type( $attachment_id ) ?: '';
			}
		}

		return array(
			'url'           => $video_url,
			'type'          => $video_type,
			'attachment_id' => $attachment_id,
		);
	}

	/**
	 * 2026-07-01: Builds bcvideolist items from main_video for native prev/next navigation.
	 */
	private function build_native_video_list_item( int $post_id ): ?array {
		$main_video = get_field( 'main_video', $post_id );

		if ( empty( $main_video ) || ! is_array( $main_video ) ) {
			return null;
		}

		$source = $this->resolve_main_video_source( $main_video );

		if ( empty( $source['attachment_id'] ) || empty( $source['url'] ) ) {
			return null;
		}

		$terms    = get_the_terms( $post_id, 'video-show-category' );
		$cat_name = ( $terms && ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) ? $terms[0]->name : '';

		return array(
			'id'      => (string) $source['attachment_id'],
			'url'     => $source['url'],
			'type'    => $source['type'],
			'post_id' => $post_id,
			'title'   => html_entity_decode( get_the_title( $post_id ) ),
			'cat'     => $cat_name,
		);
	}

	/**
	 * Callback function to create video popup
	 */
	public function dp_create_video_popup_html() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$post_id                 = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_STRING );
			$user_id                 = get_current_user_id();
			$user_playlists          = get_user_meta( $user_id, 'user_playlist', true );
			$user_trending_video_arr = get_user_meta( $user_id, 'trending_videos_of_user', true );
			$post_title              = get_the_title( $post_id );
			$post_desc               = get_the_excerpt( $post_id );
			$terms                   = wp_get_post_terms( $post_id, 'video-show-category' );
			$term_ids                = array();
			foreach ( $terms as $term ) {
				$term_ids[] = $term->term_id;
			}
			if ( $post_title ) {
				$words_array = explode( ' ', $post_title );
				foreach ( $words_array as $index => $value ) {
					if ( ( $index + 1 ) < count( $words_array ) ) {
						$heading1 .= $value . ' ';
					} else {
						$heading2 .= $value;
					}
				}
			}
			ob_start();
			?>
				<div class="video-popup-content" id="video-popup-content" data-post-id="<?php echo esc_attr( $post_id ); ?>">
					<div class="popup-modal-close"><i class="fas fa-times"></i></div>
					<div class="video-thumbnail">
						<?php
						$video_banner_url = get_field( 'banner_image', $post_id );
						if ( $video_banner_url ) {
							echo '<img width="1300" height="1200" src=' . esc_url( $video_banner_url ) . ' class="attachment-full size-full" alt=' . esc_attr( $post_title ) . ' decoding="async" loading="lazy">';
						} elseif ( has_post_thumbnail( $post_id ) ) {
							echo get_the_post_thumbnail( $post_id, 'full' );
						} else {
							$placeholder_image = get_field( 'placeholder_image', 'option' );
							if ( ! empty( $placeholder_image ) ) {
								echo wp_get_attachment_image( $placeholder_image, 'full' );
							}
						}
						?>
					</div>
					<div class="video-content">
						<?php if ( $heading1 || $heading2 ) { ?>
							<h2 class="post-heading"><span class="normal-text"><?php echo esc_html( $heading1 ); ?></span><span class="clipped-text"><?php echo esc_html( $heading2 ); ?></span></h2>
						<?php } ?>
						
						<div class="video-buttons">
							<div class="video-action-buttons">
								<?php
								// 2026-07-01: Watch/Resume buttons use main_video attachment ID (not Brightcove).
								$user_meta_data    = get_user_meta( $user_id, 'bc_watch_time', true );
								$main_video        = get_field( 'main_video', $post_id );
								$main_video_source = array(
									'url'           => '',
									'type'          => '',
									'attachment_id' => 0,
								);

								if ( ! empty( $main_video ) && is_array( $main_video ) ) {
									$main_video_source = $this->resolve_main_video_source( $main_video );
								}

								if ( $main_video_source['attachment_id'] > 0 ) {
									$video_key = (string) $main_video_source['attachment_id'];

									if ( ! empty( $user_meta_data ) && is_array( $user_meta_data ) && isset( $user_meta_data[ $video_key ] ) ) {
										?>
												<div class="v-watch-now video-action-button btn-main" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="play-icon"></span>Resume</div>
												<div class="play-from-beginning" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="resume-icon"></span><span class="tooltip-text">Play From Beginning</span></div>
											<?php
									} else {
										?>
												<div class="v-watch-now video-action-button btn-main" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="play-icon"></span>Watch</div>
											<?php
									}
								}
								?>
							</div>
							<?php
							if ( is_user_logged_in() ) {
								?>
									<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $post_id ); ?>">
										<div class="tooltip-text" style="display:none"></div>
										<?php
										if ( $this->dp_check_post_id_in_playlist( $user_playlists, $post_id ) ) {
											?>
												<div class="added-to-playlist pl-icon"></div>
											<?php
										} else {
											?>
												<div class="not-added-to-playlist pl-icon"></div>
											<?php
										}
										?>
									</div>
									<?php
							}

							if ( empty( $user_trending_video_arr ) ) {
								$user_trending_video_arr = array();
							}
							if ( ! in_array( $post_id, $user_trending_video_arr ) ) {
								?>
									<div class="trending-now-btn">
										<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
										<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
									</div>
								<?php
							}
							?>
						</div>
						<?php if ( $post_desc ) { ?>
							<div class="description"><?php echo esc_html( $post_desc ); ?></div>
						<?php } ?>
						
						<?php
						if ( $term_ids ) {
							$args = array(
								'post_type'      => 'deep_pockets_videos',  // Change to your post type if necessary
								'posts_per_page' => 8, // Get all posts
								'post__not_in'   => array( $post_id ),
								'tax_query'      => array( //phpcs:ignore
									array(
										'taxonomy' => 'video-show-category', // Replace with your custom taxonomy
										'field'    => 'id',
										'terms'    => $term_ids,
									),
								),
							);

							$query = new \WP_Query( $args );
							if ( $query->have_posts() ) {
								?>
									<div class="you-may-like">
										<h3 class="yml-heading">You May Also Like</h3>
										<div class='yml-content'>
											<?php
											while ( $query->have_posts() ) {
												$query->the_post();
												?>
													<div class="yml-post" id='video-modal' data-post-id="<?php echo get_the_ID(); ?>">
													<?php
													if ( has_post_thumbnail( get_the_ID() ) ) {
														echo get_the_post_thumbnail( get_the_ID(), 'full' );
													} else {
														$placeholder_image = get_field( 'placeholder_image', 'option' );
														if ( ! empty( $placeholder_image ) ) {
															echo wp_get_attachment_image( $placeholder_image, 'full' );
														}
													}
													?>
														<div class="post-content">
															<div class="post-title"><?php echo esc_html( get_the_title() ); ?></div>
															<div class="yml-watch-now-btn btn-main video-modal" data-post-id="<?php echo get_the_ID(); ?>">Watch now !</div>
														</div>
													</div>
												<?php
											}
												wp_reset_postdata(); // Restore the global post data
											?>
										</div>
									</div>
								<?php
							} else {
								echo '';
							}
						}
						?>
					</div>
				</div>
			<?php
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to add post_id into playlist.
	 */
	public function dp_add_item_to_playlist() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$playlist_slug  = filter_input( INPUT_POST, 'plslug', FILTER_SANITIZE_STRING );
			$post_id        = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_STRING );
			$series_id      = filter_input( INPUT_POST, 'sid', FILTER_SANITIZE_STRING );
			$user_id        = get_current_user_id();
			$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
			$recently_added = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
			if ( ! empty( $post_id ) && ! empty( $user_playlists ) ) {
				if ( array_key_exists( $playlist_slug, $user_playlists ) ) {
					if ( in_array( $post_id, $user_playlists[ $playlist_slug ]['video'], true ) ) {
						echo 'Video already there in the Playlist !!';
					} else {
						$user_playlists[ $playlist_slug ]['video'][] = $post_id;

						update_user_meta( $user_id, 'user_playlist', $user_playlists );
						echo 'success';
					}
					if ( ! is_array( $recently_added ) ) {
						$recently_added = array();
					}
					$recently_added[] = $post_id;
					update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
				} else {
					echo 'Playlist not exist !!';
				}
			}

			if ( ! empty( $series_id ) && ! empty( $user_playlists ) ) {
				if ( array_key_exists( $playlist_slug, $user_playlists ) ) {
					if ( in_array( $series_id, $user_playlists[ $playlist_slug ]['series'], true ) ) {
						echo 'Series already there in the Playlist !!';
					} else {
						$user_playlists[ $playlist_slug ]['series'][] = $series_id;

						update_user_meta( $user_id, 'user_playlist', $user_playlists );
						echo 'success';
					}
				} else {
					echo 'Playlist not exist !!';
				}
			}
		}
		wp_die();
	}

	/**
	 * Callback function to remove post_id from playlist.
	 */
	public function dp_remove_item_from_playlist() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$post_id        = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_STRING );
			$series_id      = filter_input( INPUT_POST, 'sid', FILTER_SANITIZE_STRING );
			$user_id        = get_current_user_id();
			$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
			$recently_added = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
			$updated_array  = array();
			if ( ! empty( $post_id ) && ! empty( $user_playlists ) ) {
				// updating playlist
				$updated_array = $this->dp_remove_post_id( $user_playlists, $post_id );
				update_user_meta( $user_id, 'user_playlist', $updated_array );

				// updating recent added video list
				$index = array_search( $post_id, $recently_added, true );
				if ( $index !== false ) {
					unset( $recently_added[ $index ] );
					$recently_added = array_values( $recently_added );
				}
				update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
				echo 'success';
			}

			if ( ! empty( $series_id ) && ! empty( $user_playlists ) ) {
				// updating playlist
				$updated_array = $this->dp_remove_series_id( $user_playlists, $series_id );
				update_user_meta( $user_id, 'user_playlist', $updated_array );
				echo 'success';
			}
		}
		wp_die();
	}

	/**
	 * Function to check post_id in playlist.
	 */
	public function dp_check_post_id_in_playlist( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $item ) {
				if ( is_array( $item ) && array_key_exists( 'video', $item ) && in_array( $value, $item['video'], true ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Function to remove post id from playlist
	 */
	public function dp_remove_post_id( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $key => $subArray ) {
				if ( is_array( $subArray ) && isset( $subArray['video'] ) && is_array( $subArray['video'] ) ) {
					$filteredVideo = array_filter( $subArray['video'], 'is_numeric' );
					if ( ! empty( $filteredVideo ) ) {
						$index = array_search( $value, $subArray['video'], true );
						if ( $index !== false ) {
							unset( $array[ $key ]['video'][ $index ] );
							$array[ $key ]['video'] = array_values( $array[ $key ]['video'] );
						}
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Callback function to create season popup
	 */
	public function dp_create_series_popup_html() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$series_id      = filter_input( INPUT_POST, 'series_id', FILTER_SANITIZE_STRING );
			$user_id        = get_current_user_id();
			$user_playlists = get_user_meta( $user_id, 'user_playlist', true );
			$series_term    = get_term( $series_id );
			if ( $series_term && ! is_wp_error( $series_term ) ) {
				$series_title       = $series_term->name;
				$series_description = $series_term->description;
				if ( $series_title ) {
					$words_array = explode( ' ', $series_title );
					foreach ( $words_array as $index => $value ) {
						if ( ( $index + 1 ) < count( $words_array ) ) {
							$heading1 .= $value . ' ';
						} else {
							$heading2 .= $value;
						}
					}
				}
			}
			$season_ids = array();
			if ( $series_id ) {
				$args  = array(
					'post_type'      => 'deep_pockets_videos',
					'posts_per_page' => -1,
					'tax_query'      => array( //phpcs:ignore
						array(
							'taxonomy' => 'video-show-category',
							'field'    => 'id',
							'terms'    => $series_id,
						),
					),
					'orderby'        => 'term_id', // Order by term ID
					'order'          => 'ASC',
				);
				$query = new \WP_Query( $args );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$terms = get_the_terms( get_the_ID(), 'video-season-category' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							// Loop through terms to get unique term IDs
							foreach ( $terms as $term ) {
								if ( ! in_array( $term->term_id, $season_ids, true ) ) {
									$season_ids[] = $term->term_id;
								}
							}
						}
					}
					wp_reset_postdata(); // Restore the global post data
				}
			}

			ob_start();
			?>
				<div class="series-popup-content" id="series-popup-content" data-series-id="<?php echo esc_attr( $series_id ); ?>">
					<div class="popup-modal-close"><i class="fas fa-times"></i></div>
						<div class="series-thumbnail">
							<?php
							$series_thumbnail_url         = get_field( 'add_feature_image', 'video-show-category_' . $series_id );
							$series_banner_url            = get_field( 'add_banner_image', 'video-show-category_' . $series_id );
							$term_trailer_video           = get_field( 'term_trailer_video', 'video-show-category_' . $series_id );
							$term_trailer_video_url       = '';
							$term_trailer_video_type      = '';
							$banner_placeholder_image     = get_field( 'placeholder_image', 'option' );
							$banner_placeholder_image_url = wp_get_attachment_image_url( $banner_placeholder_image, 'full' );
							$video_img_url                = ! empty( $series_banner_url ) ? $series_banner_url : $series_thumbnail_url;
							$show_banner_url              = ! empty( $video_img_url ) ? $video_img_url : $banner_placeholder_image_url;

							if ( ! empty( $term_trailer_video ) && is_array( $term_trailer_video ) ) {
								$trailer_source          = $this->resolve_trailer_video_source( $term_trailer_video );
								$term_trailer_video_url  = $trailer_source['url'];
								$term_trailer_video_type = $trailer_source['type'];
							}

							if ( ! empty( $term_trailer_video_url ) ) {
								?>
								<div class="series-banner">
									<img src="<?php echo esc_url( $show_banner_url ); ?>" alt="banner" width="1400" height="600">
								</div>
								<video class="vjs-fluid" playsinline preload="metadata">
									<source
										src="<?php echo esc_url( $term_trailer_video_url ); ?>"
										<?php echo ! empty( $term_trailer_video_type ) ? 'type="' . esc_attr( $term_trailer_video_type ) . '"' : ''; ?>
									>
								</video>
								<div class="media-settings">
									<div class="mute-button"></div>
								</div>
								<?php
							} else {
								?>
									<img src="<?php echo esc_url( $show_banner_url ); ?>" alt="banner" width="1400" height="600">
								<?php
							}

							?>
						</div>
						<div class="series-content">
							<?php if ( $series_title ) { ?>
								<h2 class="term-heading"><span class="normal-text"><?php echo esc_html( $heading1 ); ?></span><span class="clipped-text"><?php echo esc_html( $heading2 ); ?></span></h2>
							<?php } ?>
							<div class="series-buttons">
								<?php if ( ! empty( $term_trailer_video_url ) ) { ?>
									<div
										class="v-watch-trailer btn-main"
										data-trailer-url="<?php echo esc_url( $term_trailer_video_url ); ?>"
										<?php if ( ! empty( $term_trailer_video_type ) ) : ?>
											data-trailer-type="<?php echo esc_attr( $term_trailer_video_type ); ?>"
										<?php endif; ?>
									><span class="play-icon"></span>Watch Trailer</div>
								<?php } ?>
								<?php
								if ( is_user_logged_in() ) {
									?>
										<div class="playlist-popup-button" data-series-id="<?php echo esc_attr( $series_id ); ?>">
											<div class="tooltip-text" style="display:none"></div>
											<?php
											if ( $this->dp_check_term_id_in_playlist( $user_playlists, $series_id ) ) {
												?>
													<div class="added-to-playlist pl-icon"></div>
												<?php
											} else {
												?>
													<div class="not-added-to-playlist pl-icon"></div>
												<?php
											}
											?>
										</div>
									<?php
								}
								?>
							</div>
							<?php if ( $series_description ) { ?>	
								<div class="description"><?php echo esc_html( $series_description ); ?></div>
							<?php } ?>

							<?php if ( ! empty( $season_ids ) ) { ?>
								<div class="series-seasons">
									<div class="season-tab-wrapper">
										<?php
										foreach ( $season_ids as $season_id ) {
											$season_term = get_term( $season_id );
											if ( $season_term && ! is_wp_error( $season_term ) ) {
												$season_title = $season_term->name;
											}
											$series_term = get_term( $series_id );
											if ( $series_term && ! is_wp_error( $series_term ) ) {
												$series_title = $series_term->name;
											}
											?>
													<div class="season-tab" data-season-id="<?php echo esc_attr( $season_id ); ?>">
														<p class="season-tab-title btn-main btn-transparent"><?php echo esc_html( $season_title ); ?></p>
													</div>      
												<?php
										}
										?>
									</div>
									<div class="season-episodes">
										<?php
											$first_season_id = $season_ids[0];
											$args            = array(
												'post_type' => 'deep_pockets_videos',  // Change to your post type if necessary
												'posts_per_page' => -1, // Get all posts
												'order'   => 'ASC', // Order in ascending order.
												'orderby' => 'post_date', // Order by term ID
												'tax_query'      => array( //phpcs:ignore
													'relation' => 'AND',
													array(
														'taxonomy' => 'video-season-category',
														'field'    => 'id',
														'terms'    => $first_season_id,
													),
													array(
														'taxonomy' => 'video-show-category',
														'field'    => 'id',
														'terms'    => $series_id,
													),
												),
											);
											$query           = new \WP_Query( $args );
											if ( $query->have_posts() ) {
												while ( $query->have_posts() ) {
													$query->the_post();
													?>
													<div class="episode play-from-beginning" data-post-id="<?php echo get_the_ID(); ?>">
														<div class="episode-img">
															<?php
															if ( has_post_thumbnail( get_the_ID() ) ) {
																echo get_the_post_thumbnail( get_the_ID(), 'full' );
															} else {
																$placeholder_image = get_field( 'placeholder_image', 'option' );
																if ( ! empty( $placeholder_image ) ) {
																	echo wp_get_attachment_image( $placeholder_image, 'full' );
																}
															}
															?>
														</div>
														<div class="episode-content">
															<div class="episode-title"><?php echo esc_html( get_the_title() ); ?></div>
															<div class="episode-desc"><?php echo esc_html( get_the_excerpt() ); ?></div>
															<?php
															$episode_main_video = get_field( 'main_video', get_the_ID() );
															if ( ! empty( $episode_main_video ) && is_array( $episode_main_video ) ) {
																$episode_main_source = $this->resolve_main_video_source( $episode_main_video );
																if ( $episode_main_source['attachment_id'] > 0 ) {
																	$episode_metadata = wp_get_attachment_metadata( $episode_main_source['attachment_id'] );
																	if ( ! empty( $episode_metadata['length'] ) ) {
																		?>
																		<div class="episode-duration">
																			<?php echo esc_html( format_video_duration( (int) $episode_metadata['length'] ) ); ?>
																		</div>
																		<?php
																	}
																}
															}
															?>
														</div>
													</div>
													<?php
												}
												wp_reset_postdata(); // Restore the global post data
											} else {
												echo '';
											}
											?>
									</div>
								</div>
							<?php } ?>
						</div>
				</div>
			<?php
		}
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Callback function to update season popup
	 */
	public function dp_update_season_list() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$season_id = filter_input( INPUT_POST, 'season_id', FILTER_SANITIZE_STRING );
			$series_id = filter_input( INPUT_POST, 'series_id', FILTER_SANITIZE_STRING );
			$args      = array(
				'post_type'      => 'deep_pockets_videos',  // Change to your post type if necessary
				'posts_per_page' => -1, // Get all posts
				'order'          => 'ASC', // Order in ascending order.
				'orderby'        => 'post_date', // Order by term ID
				'tax_query'      => array( //phpcs:ignore
					'relation' => 'AND',
					array(
						'taxonomy' => 'video-season-category',
						'field'    => 'id',
						'terms'    => $season_id,
					),
					array(
						'taxonomy' => 'video-show-category',
						'field'    => 'id',
						'terms'    => $series_id,
					),
				),
			);
			$query     = new \WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					?>
					<div class="episode play-from-beginning" data-post-id="<?php echo get_the_ID(); ?>">
						<div class="episode-img">
							<?php
							if ( has_post_thumbnail( get_the_ID() ) ) {
								echo get_the_post_thumbnail( get_the_ID(), 'full' );
							} else {
								$placeholder_image = get_field( 'placeholder_image', 'option' );
								if ( ! empty( $placeholder_image ) ) {
									echo wp_get_attachment_image( $placeholder_image, 'full' );
								}
							}
							?>
						</div>
						<div class="episode-content">
							<div class="episode-title"><?php echo esc_html( get_the_title() ); ?></div>
							<div class="episode-desc"><?php echo esc_html( get_the_excerpt() ); ?></div>
							<?php
							$episode_main_video = get_field( 'main_video', get_the_ID() );
							if ( ! empty( $episode_main_video ) && is_array( $episode_main_video ) ) {
								$episode_main_source = $this->resolve_main_video_source( $episode_main_video );
								if ( $episode_main_source['attachment_id'] > 0 ) {
									$episode_metadata = wp_get_attachment_metadata( $episode_main_source['attachment_id'] );
									if ( ! empty( $episode_metadata['length'] ) ) {
										?>
										<div class="episode-duration">
											<?php echo esc_html( format_video_duration( (int) $episode_metadata['length'] ) ); ?>
										</div>
										<?php
									}
								}
							}
							?>
						</div>
					</div>
					<?php
				}
				wp_reset_postdata(); // Restore the global post data
			} else {
				echo '';
			}
		}
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Function to check post_id in playlist.
	 */
	public function dp_check_term_id_in_playlist( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $item ) {
				if ( is_array( $item ) && array_key_exists( 'series', $item ) && in_array( $value, $item['series'], true ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Function to remove post id from playlist
	 */
	public function dp_remove_series_id( $array, $value ) {
		if ( is_array( $array ) && ! empty( $array ) ) {
			foreach ( $array as $key => $subArray ) {
				if ( is_array( $subArray ) && isset( $subArray['series'] ) && is_array( $subArray['series'] ) ) {
					if ( ! empty( $subArray['series'] ) ) {
						$index = array_search( $value, $subArray['series'], true );
						if ( $index !== false ) {
							unset( $array[ $key ]['series'][ $index ] );
							$array[ $key ]['series'] = array_values( $array[ $key ]['series'] );
						}
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Ajax call back function to add the video ids in trending videos meta
	 */
	public function dp_trending_videos_listing_callback() {
		// Check if the request is coming from a valid user
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		// Get the video ID from the AJAX request
		$video_id = filter_input( INPUT_GET, 'video_id', FILTER_SANITIZE_STRING );

		// Get the existing array of video clicks from options
		$existing_trending_videos = get_option( 'trending_videos', array() );

		// Check if the video ID is already in the array
		if ( isset( $existing_trending_videos[ $video_id ] ) ) {
			// If it exists, increment the click count
			$existing_trending_videos[ $video_id ]++;
		} else {
			// If it doesn't exist, set the click count to 1
			$existing_trending_videos[ $video_id ] = 1;
		}

		// Update the options with the new array of video clicks
		update_option( 'trending_videos', $existing_trending_videos );

		// Get the current user ID
		$user_id = get_current_user_id();

		// Get the existing array of video IDs from user meta
		$existing_trending_videos_of_user = get_user_meta( $user_id, 'trending_videos_of_user', true );

		// If the array doesn't exist, create a new one
		if ( ! $existing_trending_videos_of_user ) {
			$existing_trending_videos_of_user = array();
		}

		// Add the new video ID to the array
		$existing_trending_videos_of_user[] = $video_id;

		// Remove duplicates
		$existing_trending_videos_of_user = array_unique( $existing_trending_videos_of_user );

		// Update the user meta with the new array of video IDs
		update_user_meta( $user_id, 'trending_videos_of_user', $existing_trending_videos_of_user );

		wp_send_json_success( 'Video click count updated successfully' );
		wp_die();
	}

	public function dp_create_watch_video_html_callback() {
		// 2026-07-01: Returns native <video> HTML from ACF main_video for .v-watch-now popup playback.
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$post_id             = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_STRING );
			$main_video          = get_field( 'main_video', $post_id );
			$main_video_source   = array(
				'url'           => '',
				'type'          => '',
				'attachment_id' => 0,
			);
			$episode_number      = get_field( 'season_episode_number', $post_id );

			if ( ! empty( $main_video ) && is_array( $main_video ) ) {
				$main_video_source = $this->resolve_main_video_source( $main_video );
			}

			if ( empty( $main_video_source['url'] ) ) {
				wp_die();
			}

			// Get the parent and child taxonomy terms
			$terms       = get_the_terms( $post_id, 'video-show-category' );
			$parent_term = '';
			$child_term  = '';

			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $term->parent == 0 ) {
						$parent_term = $term->slug;
					} else {
						$child_term = $term->slug;
					}
				}
			}
			$seasons       = get_the_terms( $post_id, 'video-season-category' );
			$parent_season = '';
			$child_season  = '';

			if ( $seasons && ! is_wp_error( $seasons ) ) {
				foreach ( $seasons as $season ) {
					if ( $season->parent == 0 ) {
						$parent_season = $season->slug;
					} else {
						$child_season = $season->slug;
					}
				}
			}

			ob_start();
			?>
				<div class="watch-video-loader" id="watch-video-loader"></div>
				<div class="watch-video-content" id="watch-video-content">
					<div class="watch-video-back-button" data-post-id="<?php echo esc_attr( $post_id ); ?>"></div>
					<video
						controls
						playsinline
						preload="metadata"
						data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
						data-video-post-id="<?php echo esc_attr( $post_id ); ?>"
						data-parent-show="<?php echo esc_attr( $parent_term ); ?>"
						data-child-show="<?php echo esc_attr( $child_term ); ?>"
						data-parent-season="<?php echo esc_attr( $parent_season ); ?>"
						data-child-season="<?php echo esc_attr( $child_season ); ?>"
						data-season-episode="<?php echo esc_attr( $episode_number ); ?>"
					>
						<source
							src="<?php echo esc_url( $main_video_source['url'] ); ?>"
							<?php echo ! empty( $main_video_source['type'] ) ? 'type="' . esc_attr( $main_video_source['type'] ) . '"' : ''; ?>
						>
					</video>
				</div>
			<?php
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	public function dp_update_video_action_buttons_callback() {
		// 2026-07-01: Refreshes Watch/Resume labels after playback using main_video attachment ID.
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$post_id           = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_STRING );
			$user_id           = get_current_user_id();
			$user_meta_data    = get_user_meta( $user_id, 'bc_watch_time', true );
			$main_video        = get_field( 'main_video', $post_id );
			$main_video_source = array(
				'url'           => '',
				'type'          => '',
				'attachment_id' => 0,
			);

			if ( ! empty( $main_video ) && is_array( $main_video ) ) {
				$main_video_source = $this->resolve_main_video_source( $main_video );
			}

			ob_start();

			if ( $main_video_source['attachment_id'] > 0 ) {
				$video_key = (string) $main_video_source['attachment_id'];

				if ( ! empty( $user_meta_data ) && is_array( $user_meta_data ) && isset( $user_meta_data[ $video_key ] ) ) {
					?>
					<div class="v-watch-now resume-now video-action-button btn-main" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="play-icon"></span>Resume</div>
					<div class="play-from-beginning" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="resume-icon"></span><span class="tooltip-text">Play From Beginning</span></div>
					<?php
				} else {
					?>
					<div class="v-watch-now video-action-button btn-main" data-post-id="<?php echo esc_attr( $post_id ); ?>"><span class="play-icon"></span>Watch</div>
					<?php
				}
			}
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	public function dp_create_series_trailer_html_callback() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$trailer_url  = filter_input( INPUT_POST, 'trailer_url', FILTER_SANITIZE_URL );
			$trailer_type = filter_input( INPUT_POST, 'trailer_type', FILTER_SANITIZE_STRING );

			if ( empty( $trailer_url ) ) {
				wp_die();
			}

			ob_start();
			?>
				<div class="watch-video-loader" id="watch-video-loader"></div>
				<div class="watch-video-content watch-trailer-content" id="watch-video-content">
					<div class="watch-trailer-back-button"></div>
					<video controls playsinline preload="metadata">
						<source
							src="<?php echo esc_url( $trailer_url ); ?>"
							<?php echo ! empty( $trailer_type ) ? 'type="' . esc_attr( $trailer_type ) . '"' : ''; ?>
						>
					</video>
				</div>
			<?php
		}

		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Set the video time in the user meta data.
	 * @return void
	 */
	public function dp_brightcove_video_set_time() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$currentVideoTime = ( isset( $_POST['currentVideoTime'] ) ) ? wp_kses_post( $_POST['currentVideoTime'] ) : '';
			$videoID          = ( isset( $_POST['videoID'] ) ) ? wp_kses_post( $_POST['videoID'] ) : '';
			$current_user     = wp_get_current_user();
			$user_id          = $current_user->ID;
			$video_duration   = filter_input( INPUT_POST, 'totalDuration', FILTER_SANITIZE_STRING );

			if ( empty( $currentVideoTime ) && ! empty( $videoID ) ) {
				$user_meta_data = get_user_meta( $user_id, 'bc_watch_time', true );

				unset( $user_meta_data[ $videoID ] );
				update_user_meta( $user_id, 'bc_watch_time', $user_meta_data );

			}

			if ( ! empty( $video_duration ) && ! empty( $videoID ) ) {

				$bc_video_duration_meta = get_user_meta( $user_id, 'bc_video_duration_time', true );
				// If the metadata doesn't exist yet, initialize an empty array

				if ( empty( $bc_video_duration_meta ) || ! is_array( $bc_video_duration_meta ) ) {
					$bc_video_duration_meta = array();
				}

				// Add or update the video data
				$bc_video_duration_meta[ $videoID ] = $video_duration;

				// Set the user metadata
				update_user_meta( $user_id, 'bc_video_duration_time', $bc_video_duration_meta );

			}

			if ( ! empty( $currentVideoTime ) && ! empty( $videoID ) ) {

				// Get the current user metadata
				$user_meta_data = get_user_meta( $user_id, 'bc_watch_time', true );

				// If the metadata doesn't exist yet, initialize an empty array
				if ( empty( $user_meta_data ) || ! is_array( $user_meta_data ) ) {
					$user_meta_data = array();
				}

				// Add or update the video data
				$user_meta_data[ $videoID ] = $currentVideoTime;

				// Set the user metadata
				update_user_meta( $user_id, 'bc_watch_time', $user_meta_data );

				exit;
			}
		}
	}

	/**
	 * Get the video time from the user meta data.
	 */
	public function dp_brightcove_video_get_time() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$videoID = ( isset( $_POST['videoID'] ) ) ? wp_kses_post( $_POST['videoID'] ) : '';
			if ( ! empty( $videoID ) ) {
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;

				$user_meta_data = get_user_meta( $user_id, 'bc_watch_time', true );

				if ( isset( $user_meta_data[ $videoID ] ) ) {
					$videoData = $user_meta_data[ $videoID ];

					echo wp_json_encode( $videoData );
					exit;
				}
			}
		}
	}

	/**
	 * This function register API endpoint for player Category filter
	 */
	public function dp_player_api_register() {

		register_rest_route(
			'wp/v2',
			'bcvideolist',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dp_brightcove_get_video_list_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * brightcove videoid list for prev next video functionality
	 * 2026-07-01: Now returns native main_video url/type/id from deep_pockets_videos.
	 */
	public function dp_brightcove_get_video_list_callback() {
		$videoParentShow   = ( ! empty( filter_input( INPUT_GET, 'video_parent_show', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_parent_show', FILTER_SANITIZE_STRING ) : '';
		$videoChildShow    = ( ! empty( filter_input( INPUT_GET, 'video_child_show', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_child_show', FILTER_SANITIZE_STRING ) : '';
		$videoParentSeason = ( ! empty( filter_input( INPUT_GET, 'video_parent_season', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_parent_season', FILTER_SANITIZE_STRING ) : '';
		$currentVideoID    = ( ! empty( filter_input( INPUT_GET, 'video_current_id', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_current_id', FILTER_SANITIZE_STRING ) : '';
		$videoPostID       = ( ! empty( filter_input( INPUT_GET, 'video_post_id', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_post_id', FILTER_SANITIZE_STRING ) : '';
		$videoEpisode      = ( ! empty( filter_input( INPUT_GET, 'video_season_episode', FILTER_SANITIZE_STRING ) ) ) ? filter_input( INPUT_GET, 'video_season_episode', FILTER_SANITIZE_STRING ) : '';
		$video_IDs         = array();

		if ( $videoParentSeason && 'financial-masterclass' === $videoParentShow ) {

			// Use WP_Query to retrieve video posts from your database
			$args = array(
				'post_type'      => 'deep_pockets_videos',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => 'season_episode_number',
				'orderby'        => 'meta_value_num', // Order by episode number as a numeric value
				'order'          => 'ASC', // Change to 'DESC' if you want to reverse the order
			);

			$args['tax_query']             = array( //phpcs:ignore
				array(
					'taxonomy' => 'video-season-category',
					'field'    => 'slug',
					'terms'    => $videoParentSeason,
				),
			);
			$args['tax_query'][]           = array( //phpcs:ignore
				'taxonomy' => 'video-show-category',
				'field'    => 'slug',
				'terms'    => $videoChildShow,
			);
			$args['tax_query']['relation'] = 'AND'; //phpcs:ignore

			$video_query = new \WP_Query( $args );

			if ( $video_query->have_posts() ) {
				while ( $video_query->have_posts() ) {
					$video_query->the_post();
					$post_id      = get_the_ID();
					$episode_data = get_field( 'season_episode_number', $post_id ); // Replace with the actual meta key for episode number
					$episode_num  = $episode_data ? intval( $episode_data ) : 0;
					$video        = $this->build_native_video_list_item( $post_id );

					// Check if episode number is not blank or 0 before adding to the result array
					if ( null !== $video && $episode_num !== '' && $episode_num !== 0 ) {
						$terms = get_the_terms( $post_id, 'video-season-category' );

						$video['cat']            = ( $terms && ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) ? $terms[0]->name : $video['cat'];
						$video['episode_number'] = $episode_num;
						$video_IDs[]             = $video;
					}
				}
			}

			wp_reset_postdata();

			// Sort the video list based on episode number
			usort(
				$video_IDs,
				function( $a, $b ) {
					return $a['episode_number'] - $b['episode_number'];
				}
			);

			// Return the video list as a JSON response
			wp_send_json( $video_IDs );
		} else {
			// Use WP_Query to retrieve video posts from your database
			$args = array(
				'post_type'      => 'deep_pockets_videos',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);

			if ( ! empty( $videoChildShow ) ) {
				$args['tax_query'] = array(//phpcs:ignore
					array(
						'taxonomy' => 'video-show-category',
						'field'    => 'slug',
						'terms'    => $videoChildShow,
					),

				);
			} else {
				$args['tax_query'] = array( //phpcs:ignore
					array(
						'taxonomy' => 'video-show-category',
						'field'    => 'slug',
						'terms'    => $videoParentShow,
					),

				);
			}
			$video_query = new \WP_Query( $args );

			if ( $video_query->have_posts() ) {
				while ( $video_query->have_posts() ) {
					$video_query->the_post();
					$post_id = get_the_ID();
					$video   = $this->build_native_video_list_item( $post_id );

					if ( null !== $video ) {
						$video_IDs[] = $video;
					}
				}
			}

			wp_reset_postdata();
			// Assuming $targetVideoId is the video_id you want to place at the beginning
			$targetVideoIndex = array_search( $currentVideoID, array_column( $video_IDs, 'id' ) );

			if ( $targetVideoIndex !== false ) {
				// Move the target video to the beginning
				$targetVideo = $video_IDs[ $targetVideoIndex ];
				unset( $video_IDs[ $targetVideoIndex ] );
				array_unshift( $video_IDs, $targetVideo );

				// Reset array keys to start from 0
				$video_IDs = array_values( $video_IDs );
			}

			// Return the video list as a JSON response
			wp_send_json( $video_IDs );
		}

	}

	public function dp_set_video_duration_time_callback() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'loadmore_post_nonce' ) ) {
			$video_duration         = filter_input( INPUT_POST, 'duration', FILTER_SANITIZE_STRING );
			$video_id               = filter_input( INPUT_POST, 'videoId', FILTER_SANITIZE_STRING );
			$current_user           = wp_get_current_user();
			$user_id                = $current_user->ID;
			$bc_video_duration_meta = get_user_meta( $user_id, 'bc_video_duration_time', true );

			// If the metadata doesn't exist yet, initialize an empty array
			if ( empty( $bc_video_duration_meta ) || ! is_array( $bc_video_duration_meta ) ) {
				$bc_video_duration_meta = array();
			}
			if ( ! empty( $video_duration ) && ! empty( $video_id ) ) {
				// Add or update the video data
				$bc_video_duration_meta[ $video_id ] = $video_duration;

				// Set the user metadata
				update_user_meta( $user_id, 'bc_video_duration_time', $bc_video_duration_meta );
			}
		}
		exit;
	}
}
