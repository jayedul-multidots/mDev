<?php
/**
 * Registers the vaultrix/our-team block.
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
 *  Class for the vaultrix/our-team block.
 */
class Our_Team_Inner extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'our-team-inner';
	}
}
