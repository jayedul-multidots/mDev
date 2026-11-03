/**
 * Dynamic block: markup is rendered by render.php, only inner blocks are saved.
 */
import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
