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
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';

import { prjdpColors } from '../common';

import metadata from './block.json';

import './editor.scss';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const { blockID, heading, headingColor, selectedCategory } = attributes;
	const [ videosCategoriesList, setvideosCategoriesList ] = useState( [] );
	useEffect( () => {
		setAttributes( {
			blockID: `trending-now-section-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [ blockID, heading, headingColor, selectedCategory ] );

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/video_categories',
		} ).then( ( videoCategories ) => {
			const videoCategoriesOption = [];
			videoCategoriesOption.push( {
				label: __( 'Select Video Show', 'vaultrix' ),
				value: '',
			} );
			videoCategories.forEach( function ( item ) {
				videoCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setTimeout(
				function () {
					setvideosCategoriesList( videoCategoriesOption );
				}.bind( this ),
				600
			);
		} );
	}, [] );

	const initSlider = () => {
		jQuery( `#${ blockID } .trending-now-section__video-series-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 4,
				slidesToScroll: 1,
				dots: false,
				arrows: true,
				autoplay: true,
			} );
	};
	initSlider();
	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
						<SelectControl
							label={ __( 'Select Video Show', 'vaultrix' ) }
							value={ selectedCategory }
							options={ videosCategoriesList }
							onChange={ ( value ) => {
								setAttributes( {
									selectedCategory: value,
								} );
							} }
						/>

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
					headingColor,
					selectedCategory,
				} }
			/>
		</>
	);
}
