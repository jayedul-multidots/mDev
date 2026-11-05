/* eslint-disable no-unused-expressions */
/* eslint-disable no-alert */

import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import { PanelBody, ToggleControl, SelectControl } from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

import metadata from './block.json';
import { useEffect, useState } from '@wordpress/element';
import { prjdpColors } from '../common';
import './editor.scss';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		autoplay,
		arrows,
		dots,
		loopSlide,
		cardTitleColor,
		selectedParentCategory,
	} = attributes;

	const [ parentCategoriesList, setParentCategoriesList ] = useState( [] );

	useEffect( () => {
		setAttributes( {
			blockID: `cards-slider-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/video_categories',
		} ).then( ( videoCategories ) => {
			const parentCategoriesOption = [];
			parentCategoriesOption.push( {
				label: __( 'Select Parent Category', 'vaultrix' ),
				value: '',
			} );
			videoCategories.forEach( function ( item ) {
				parentCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setParentCategoriesList( parentCategoriesOption );
		} );
	}, [] );

	const initSlider = () => {
		jQuery( `#${ blockID } .mega-menu-card-slider-main` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: loopSlide,
				slidesToShow: 4,
				slidesToScroll: 1,
				dots,
				arrows,
				autoplay,
			} );
	};

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [
		blockID,
		autoplay,
		arrows,
		dots,
		loopSlide,
		cardTitleColor,
		selectedParentCategory,
	] );

	initSlider();

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Slider Settings', 'deep-pocket' ) }
						initialOpen={ true }
					>
						<SelectControl
							label={ __(
								'Select Parent Category',
								'vaultrix'
							) }
							value={ selectedParentCategory }
							options={ parentCategoriesList }
							onChange={ ( value ) => {
								setAttributes( {
									selectedParentCategory: value,
								} );
							} }
						/>
						<ToggleControl
							label={ __( 'Autoplay', 'deep-pocket' ) }
							checked={ autoplay }
							onChange={ ( value ) =>
								setAttributes( { autoplay: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Arrows', 'deep-pocket' ) }
							checked={ arrows }
							onChange={ ( value ) =>
								setAttributes( { arrows: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Dots', 'deep-pocket' ) }
							checked={ dots }
							onChange={ ( value ) =>
								setAttributes( { dots: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Loop', 'deep-pocket' ) }
							checked={ loopSlide }
							onChange={ ( value ) =>
								setAttributes( { loopSlide: value } )
							}
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Color Settings', 'deep-pocket' ) }
						initialOpen={ false }
					>
						<PanelColorSettings
							colorSettings={ [
								{
									value: cardTitleColor,
									onChange: ( value ) => {
										setAttributes( {
											cardTitleColor: value,
										} );
									},
									label: __(
										'Card Title Color',
										'deep-pocket'
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
					autoplay,
					arrows,
					dots,
					loopSlide,
					cardTitleColor,
					selectedParentCategory,
				} }
			/>
		</>
	);
}
