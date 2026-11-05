/* eslint-disable no-unused-expressions */
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
import { PanelBody, TextControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import metadata from './block.json';
import { prjdpColors } from '../common';
import './editor.scss';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		viewAllText,
		viewAllLink,
		headingColor,
		bgColor,
	} = attributes;

	useEffect( () => {
		setAttributes( {
			blockID: `my-playlist-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [ blockID, heading, viewAllText, viewAllLink, headingColor, bgColor ] );

	const initSlider = () => {
		jQuery( `#${ blockID } .playlist-listing__series-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 4,
				slidesToScroll: 1,
				autoplay: true,
				arrows: true,
				dots: false,
			} );

		jQuery( `#${ blockID } .playlist-listing__series-episodes-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 6,
				slidesToScroll: 1,
				autoplay: true,
				arrows: true,
				dots: false,
			} );

		jQuery( `#${ blockID } .playlist-listing__video-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 6,
				slidesToScroll: 1,
				autoplay: true,
				arrows: true,
				dots: false,
			} );
	};

	initSlider();
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
						<TextControl
							label={ __( 'View All Text', 'vaultrix' ) }
							placeholder={ __(
								'Enter View All Text',
								'vaultrix'
							) }
							value={ viewAllText }
							onChange={ ( value ) =>
								setAttributes( { viewAllText: value } )
							}
						/>
						<TextControl
							label={ __( 'View All Text Link', 'vaultrix' ) }
							placeholder={ __(
								'Enter View All Link',
								'vaultrix'
							) }
							value={ viewAllLink }
							onChange={ ( value ) =>
								setAttributes( { viewAllLink: value } )
							}
						/>
						<div>
							<b>{ __( 'Note: ', 'vaultrix' ) }</b>
							<span>
								{ __(
									'The "View All" link will be displayed if there are more than 4 videos in the user\'s playlist.',
									'vaultrix'
								) }
							</span>
						</div>
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
					heading,
					viewAllText,
					viewAllLink,
					bgColor,
					headingColor,
				} }
			/>
		</>
	);
}
