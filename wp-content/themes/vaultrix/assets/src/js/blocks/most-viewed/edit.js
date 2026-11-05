/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the server side render element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-server-side-render/
 */
import ServerSideRender from '@wordpress/server-side-render';

/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import { useEffect } from '@wordpress/element';

import { prjdpColors } from '../common';

import metadata from './block.json';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		seeAllLinkText,
		seeAllLink,
		showSeeAllLink,
		headingColor,
	} = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `most-viewed-section-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );
	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
						<TextControl
							label={ __( 'Heading', 'vaultrix' ) }
							placeholder={ __(
								'Enter Heading',
								'vaultrix'
							) }
							value={ heading }
							onChange={ ( value ) =>
								setAttributes( { heading: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show See All Link', 'vaultrix' ) }
							checked={ showSeeAllLink }
							onChange={ ( value ) =>
								setAttributes( { showSeeAllLink: value } )
							}
						/>
						{ showSeeAllLink && (
							<>
								<TextControl
									label={ __(
										'See All Link Text',
										'vaultrix'
									) }
									value={ seeAllLinkText }
									onChange={ ( value ) =>
										setAttributes( {
											seeAllLinkText: value,
										} )
									}
								/>
								<TextControl
									label={ __(
										'See All Link URL',
										'vaultrix'
									) }
									value={ seeAllLink }
									onChange={ ( value ) =>
										setAttributes( { seeAllLink: value } )
									}
								/>
							</>
						) }
					</PanelBody>
					<PanelBody
						title={ __( 'Color Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<PanelColorSettings
							colorSettings={ [
								{
									value: headingColor,
									onChange: ( value ) => {
										setAttributes( {
											headingColor: value,
										} );
									},
									label: __(
										'Heading Color',
										'vaultrix'
									),
								},
							] }
							colors={ prjdpColors }
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<ServerSideRender
				block={ metadata.name }
				attributes={ {
					blockID,
					heading,
					seeAllLinkText,
					seeAllLink,
					showSeeAllLink,
					headingColor,
				} }
			/>
		</>
	);
}
