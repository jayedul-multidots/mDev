<?php
/**
 * Registers the vaultrix/media-text block.
 *
 * @global array    $attrs   Block attributes passed to the render callback.
 * @global string   $content Block content from InnerBlocks passed to the render callback.
 * @global WP_Block $block   Block registration object.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Blocks;

use VAULTRIX\Inc\Block_Base;

/**
 *  Class for the vaultrix/media-text block.
 */

class Media_Text extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'media-text';
	}
}
