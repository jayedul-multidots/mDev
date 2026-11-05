<?php
/**
 * Registers the vaultrix/playlist-video-listing block.
 *
 * @global array    $attrs   Block attributes passed to the render callback.
 * @global string   $content Block content from InnerBlocks passed to the render callback.
 * @global WP_Block $block   Block registration object.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Blocks;

use VAULTRIX\Inc\Block_Base;
use WP_Block;

/**
 *  Class for the vaultrix/playlist-video-listing block.
 */
class Playlist_Video_Listing extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'playlist-video-listing';
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'wp_ajax_dp_remove_video_from_playlist', array( $this, 'dp_remove_video_from_playlist' ) );
		add_action( 'wp_ajax_nopriv_dp_remove_video_from_playlist', array( $this, 'dp_remove_video_from_playlist' ) );
	}

	/**
	 * Resolve the video source URL and mime type from an ACF file field.
	 *
	 * @param array $acf_video ACF file field array.
	 * @return array{url: string, type: string, attachment_id: int}
	 */
	private function resolve_video_source( array $acf_video ): array {
		$video_url     = $acf_video['url'] ?? '';
		$video_type    = $acf_video['mime_type'] ?? '';
		$attachment_id = $acf_video['ID'] ?? ( $acf_video['id'] ?? 0 );
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
	 * Render a single playlist video item.
	 *
	 * @param int $post_id Video post ID.
	 * @return void
	 */
	private function render_video_item( int $post_id ): void {
		$video_post_id           = $post_id;
		$current_user_id         = get_current_user_id();
		$user_playlists          = get_user_meta( $current_user_id, 'user_playlist', true );
		$user_trending_video_arr = get_user_meta( $current_user_id, 'trending_videos_of_user', true );

		$trailer_video_source = array(
			'url'           => '',
			'type'          => '',
			'attachment_id' => 0,
		);
		$main_video_source    = array(
			'url'           => '',
			'type'          => '',
			'attachment_id' => 0,
		);

		$trailer_video = get_field( 'trailer_video', $video_post_id );
		if ( ! empty( $trailer_video ) && is_array( $trailer_video ) ) {
			$trailer_video_source = $this->resolve_video_source( $trailer_video );
		}

		$main_video = get_field( 'main_video', $video_post_id );
		if ( ! empty( $main_video ) && is_array( $main_video ) ) {
			$main_video_source = $this->resolve_video_source( $main_video );
		}
		?>
		<div class="playlist-video-listing__video video-popup-wrapper" data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<div class="playlist-video-listing__video-thumbnail">
				<?php
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, 'full' );
				} else {
					$placeholder_image = get_field( 'placeholder_image', 'option' );
					if ( ! empty( $placeholder_image ) ) {
						echo wp_get_attachment_image( $placeholder_image, 'full' );
					}
				}
				?>
			</div>
			<div class="video-hover-popup" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
				<div class="video-mini-popup-close"><i class="fas fa-times"></i></div>
				<div class="video-hover-media-section">
				<?php
				if ( ! empty( $trailer_video_source['url'] ) ) {
					?>
					<video class="vjs-fluid" playsinline preload="metadata">
						<source
							src="<?php echo esc_url( $trailer_video_source['url'] ); ?>"
							<?php echo ! empty( $trailer_video_source['type'] ) ? 'type="' . esc_attr( $trailer_video_source['type'] ) . '"' : ''; ?>
						>
					</video>
					<div class="media-settings">
						<div class="mute-button"></div>
					</div>
					<?php
				} else {
					if ( has_post_thumbnail( $post_id ) ) {
						echo get_the_post_thumbnail( $post_id, 'full' );
					} else {
						$placeholder_image = get_field( 'placeholder_image', 'option' );
						if ( ! empty( $placeholder_image ) ) {
							echo wp_get_attachment_image( $placeholder_image, 'full' );
						}
					}
				}
				?>
				</div>
				<div class="video-hover-content-section">
					<div class="content-top">
						<div class="video-title">
							<h3 class="title"><?php echo esc_html( get_the_title( $video_post_id ) ); ?></h3>
						</div>
						<div class="video-buttons">
							<div
								class="play-from-beginning"
								data-post-id="<?php echo esc_attr( $video_post_id ); ?>"
								<?php if ( ! empty( $main_video_source['url'] ) ) : ?>
									data-video-url="<?php echo esc_url( $main_video_source['url'] ); ?>"
									<?php if ( ! empty( $main_video_source['type'] ) ) : ?>
										data-video-type="<?php echo esc_attr( $main_video_source['type'] ); ?>"
									<?php endif; ?>
									<?php if ( $main_video_source['attachment_id'] > 0 ) : ?>
										data-video-id="<?php echo esc_attr( $main_video_source['attachment_id'] ); ?>"
									<?php endif; ?>
								<?php endif; ?>
							></div>
							<?php
							if ( is_user_logged_in() ) {
								?>
									<div class="playlist-popup-button" data-post-id="<?php echo esc_attr( $video_post_id ); ?>">
										<div class="tooltip-text" style="display:none"></div>
										<?php
										if ( dp_check_post_id_in_playlist( $user_playlists, $video_post_id ) ) {
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
							if ( ! in_array( $video_post_id, $user_trending_video_arr, true ) ) {
								?>
									<div class="trending-now-btn">
										<div class="tooltip-text" style="display:none"><?php esc_html_e( 'Liked', 'vaultrix' ); ?></div>
										<img src="/wp-content/themes/vaultrix/assets/src/images/fire-img.png" alt="fire trending now button"></img>
									</div>
								<?php
							}
							?>
						</div>
					</div>
					<div class="content-bottom">
						<div class="video-description">
							<?php
								$post_content       = get_post_field( 'post_content', $video_post_id );
								$plain_text_content = wp_strip_all_tags( $post_content );
							?>
							<p><?php echo esc_html( $plain_text_content ); ?></p>
						</div>
						<div class="video-duration">
							<?php
							if ( $main_video_source['attachment_id'] > 0 ) {
								$main_video_metadata = wp_get_attachment_metadata( $main_video_source['attachment_id'] );
								if ( ! empty( $main_video_metadata['length'] ) ) {
									?>
									<div class="duration">
										<?php echo esc_html( format_video_duration( (int) $main_video_metadata['length'] ) ); ?>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the playlist video listing grid.
	 *
	 * @param array $post_ids Video post IDs.
	 * @return void
	 */
	private function render_video_listing( array $post_ids ): void {
		if ( empty( $post_ids ) ) {
			echo 'Your Playlist is empty !!';
			return;
		}
		?>
		<div class="playlist-video-listing__video-listing">
			<?php
			foreach ( $post_ids as $post_id ) {
				$this->render_video_item( (int) $post_id );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render block.
	 *
	 * @param array    $attributes   Block attributes.
	 * @param string   $content      Block content.
	 * @param WP_Block $block        Block object.
	 * @return string
	 */
	public function render_callback(
		// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		array $attributes,
		string $content,
		WP_Block $block
		// phpcs:enable
	): string {

		// get string of attributes of the features that the block supports.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'playlist-video-listing',
				'id'    => $attributes['blockID'],
			)
		);

		// attributes.
		$heading       = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$heading_style = ! empty( $attributes['headingColor'] ) ? 'style="color:' . $attributes['headingColor'] . '"' : '';
		$bg_style      = ! empty( $attributes['bgColor'] ) ? 'background: ' . $attributes['bgColor'] : 'background: #001e4e';
		$user_id       = get_current_user_id();
		$playlists     = get_user_meta( $user_id, 'user_playlist', true );
		$playlist_slug = filter_input( INPUT_GET, 'playlist', FILTER_SANITIZE_STRING );
		$post_ids      = array();
		ob_start();
		?>
		<div <?php echo wp_kses_post( $wrapper_attributes ); ?> style="<?php echo esc_attr( $bg_style ); ?>">
			<div class="container">
				<?php if ( ! empty( $playlists ) ) { ?>
				<div class="playlist-video-listing__wrapper">
					<?php if ( ! empty( $heading ) ) : ?>
						<div class="playlist-video-listing__head">
							<h1 class="playlist-video-listing__title" <?php echo wp_kses_post( $heading_style ); ?>><?php echo esc_html( $heading ); ?></h1>
						</div>
					<?php endif; ?>
					<?php
					if ( ! empty( $playlist_slug ) ) {
						$post_ids = $playlists[ $playlist_slug ]['video'];
						$this->render_video_listing( $post_ids ? $post_ids : array() );
					} else {
						foreach ( $playlists as $playlist ) {
							foreach ( $playlist['video'] as $post_id ) {
								$post_ids[] = $post_id;
							}
						}
						$this->render_video_listing( $post_ids );
					}
					?>
				</div>
					<?php
				} else {
					echo 'Your Playlist is empty !!';
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Function to remove video from playlist
	 *
	 * @return void
	 */
	public function dp_remove_video_from_playlist() {
		$post_id          = filter_input( INPUT_GET, 'pid', FILTER_SANITIZE_STRING );
		$playlist_slug    = filter_input( INPUT_GET, 'playlist_slug', FILTER_SANITIZE_STRING );
		$user_id          = get_current_user_id();
		$user_playlists   = get_user_meta( $user_id, 'user_playlist', true );
		$recently_added   = get_user_meta( $user_id, 'recently_added_videos_to_playlists', true );
		$updated_playlist = array();
		$updated_playlist = array();
		$playlists        = get_user_meta( $user_id, 'user_playlist', true );

		if ( ! empty( $post_id ) && ! empty( $user_playlists ) ) {
			// updating playlist
			$updated_playlist = $this->dp_remove_post_id( $user_playlists, $post_id );
			update_user_meta( $user_id, 'user_playlist', $updated_playlist );

			// updating recent added video list
			$index = array_search( $post_id, $recently_added, true );
			if ( $index !== false ) {
				unset( $recently_added[ $index ] );
				$recently_added = array_values( $recently_added );
			}
			update_user_meta( $user_id, 'recently_added_videos_to_playlists', $recently_added );
		}

		ob_start();
		if ( ! empty( $playlist_slug ) ) {
			$post_ids = $updated_playlist[ $playlist_slug ]['video'] ?? array();
			$this->render_video_listing( $post_ids ? $post_ids : array() );
		} else {
			$post_ids = array();
			foreach ( $updated_playlist as $playlist ) {
				foreach ( $playlist['video'] as $post_id ) {
					$post_ids[] = $post_id;
				}
			}
			$this->render_video_listing( $post_ids );
		}
		$html = ob_get_clean();
		echo $html; //phpcs:ignore
		wp_die();
	}

	/**
	 * Function to remove post id from playlist.
	 * 
	 * @param array $array Array of playlists.
	 * @param int $value Value to remove.
	 * @return array
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
}
