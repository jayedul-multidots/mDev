/**
 * Registers the mdev-gb/post-gallery block (dynamic: rendered in PHP).
 */
import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
