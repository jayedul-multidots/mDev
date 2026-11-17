<?php
/**
 * Hides the helpful poll block from the inserter outside posts and pages.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

use WP_Block_Type_Registry;
use WP_Screen;

/**
 * In the editor of any post type other than post and page, the block is
 * removed from the inserter.
 *
 * It switches off the block's `inserter` support on that screen instead of
 * filtering `allowed_block_types_all`: that filter needs the full list of
 * every other block, which would also disallow blocks registered only in
 * JavaScript. The post editor reads block definitions from the PHP registry
 * after `current_screen`, so the change reaches the inserter. The block can
 * never work on those post types anyway: VotablePolicy rejects them when
 * rendering and voting.
 *
 * @since 0.3.0
 */
final class EditorAvailability {

	public const BLOCK_NAME = 'mdev-gb/helpful-poll';

	/**
	 * Constructor.
	 *
	 * @since 0.3.0
	 *
	 * @param WP_Block_Type_Registry $registry Block type registry.
	 */
	public function __construct( private readonly WP_Block_Type_Registry $registry ) {
	}

	/**
	 * Hooked to `current_screen`.
	 *
	 * @since 0.3.0
	 *
	 * @param WP_Screen $screen Current admin screen.
	 * @return void
	 */
	public function restrict( WP_Screen $screen ): void {
		if ( 'post' !== $screen->base || VotablePolicy::supports_post_type( $screen->post_type ) ) {
			return;
		}

		$block_type = $this->registry->get_registered( self::BLOCK_NAME );

		if ( null === $block_type ) {
			return;
		}

		$supports             = is_array( $block_type->supports ) ? $block_type->supports : [];
		$supports['inserter'] = false;
		$block_type->supports = $supports;
	}
}
