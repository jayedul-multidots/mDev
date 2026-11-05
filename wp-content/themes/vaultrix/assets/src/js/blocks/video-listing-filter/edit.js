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
import { PanelBody } from '@wordpress/components';

import { useEffect } from '@wordpress/element';

import { prjdpColors } from '../common';

import metadata from './block.json';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const { blockID, bgColor } = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `video-listing-filter-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [ bgColor ] );

	const initSlider = () => {
		jQuery( `#${ blockID } .video-listing-filter__category-tab-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 6,
				slidesToScroll: 1,
				dots: false,
				variableWidth: true,
				arrows: true,
				autoplay: false,
			} );
	};
	initSlider();
	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Color Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<PanelColorSettings
							colorSettings={ [
								{
									value: bgColor,
									onChange: ( value ) => {
										setAttributes( {
											bgColor: value,
										} );
									},
									label: __(
										'Background Color',
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
					bgColor,
				} }
			/>
		</>
	);
}
