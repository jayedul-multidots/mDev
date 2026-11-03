/**
 * Editor UI for the mdev-gb/callout block.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

import { getToneOptions, normalizeTone } from './tones';
import './editor.scss';

const TEMPLATE = [
	[
		'core/paragraph',
		{ placeholder: __( 'Write the callout message…', 'mdev-gb' ) },
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const tone = normalizeTone( attributes.tone );
	const blockProps = useBlockProps( {
		className: `is-tone-${ tone }`,
		role: 'note',
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'wp-block-mdev-gb-callout__content' },
		{ template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'mdev-gb' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Tone', 'mdev-gb' ) }
						value={ tone }
						options={ getToneOptions() }
						onChange={ ( value ) =>
							setAttributes( { tone: normalizeTone( value ) } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="p"
					className="wp-block-mdev-gb-callout__heading"
					aria-label={ __( 'Callout heading', 'mdev-gb' ) }
					placeholder={ __( 'Heading (optional)', 'mdev-gb' ) }
					allowedFormats={ [
						'core/bold',
						'core/italic',
						'core/link',
					] }
					value={ attributes.heading }
					onChange={ ( heading ) => setAttributes( { heading } ) }
				/>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
