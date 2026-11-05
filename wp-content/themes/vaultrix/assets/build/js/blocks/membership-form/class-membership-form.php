<?php
/**
 * Registers the vaultrix/membership-form block.
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
 *  Class for the vaultrix/membership-form block.
 */
class Membership_Form extends Block_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_block = 'membership-form';
	}
}
