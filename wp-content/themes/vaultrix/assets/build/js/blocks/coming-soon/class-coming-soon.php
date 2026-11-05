<?php
/**
 * Registers the vaultrix/coming-soon block.
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
 *  Class for the vaultrix/coming-soon block.
 */

class Coming_Soon extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'coming-soon';
	}
}
