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
import { PanelBody, ToggleControl, RangeControl } from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';
const SortableSelect = SortableContainer( Select );

import { prjdpColors } from '../common';

import metadata from './block.json';

import './editor.scss';

function arrayMove( array, from, to ) {
	array = array.slice();
	array.splice(
		to < 0 ? array.length + to : to,
		0,
		array.splice( from, 1 )[ 0 ]
	);
	return array;
}
const SortableMultiValue = SortableElement( ( props ) => {
	const onMouseDown = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
	};
	const innerProps = { onMouseDown };
	return <components.MultiValue { ...props } innerProps={ innerProps } />;
} );

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		selectedVideoIDs,
		displayArrows,
		displayDots,
		numberOfPosts,
		videoSelectionToggle,
		enableOverlay,
		overlayColor,
		overlayOpacity,
	} = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `video-listing-hero-banner-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	const [ videosList, setvideosList ] = useState( [] );

	const videoSlider = () => {
		jQuery( `#${ blockID } .video-listing-hero-banner__boxes` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: displayDots,
				appendDots: jQuery(
					`#${ blockID } .video-listing-hero-banner__navigation`
				),
				arrows: displayArrows,
				autoplay: false,
			} );
	};

	const initSlider = () => {
		jQuery( `#${ blockID } .video-listing-hero-banner__boxes` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: true,
				appendDots: jQuery(
					`#${ blockID } .video-listing-hero-banner__navigation`
				),
				arrows: true,
				autoplay: false,
			} );
	};

	useEffect( () => {
		setTimeout( () => videoSlider(), 1500 );
	}, [
		blockID,
		selectedVideoIDs,
		displayArrows,
		displayDots,
		numberOfPosts,
		videoSelectionToggle,
		enableOverlay,
		overlayColor,
		overlayOpacity,
	] );

	useEffect( () => {
		apiFetch( {
			path: '/vaultrix/v1/video-listing-hero-banner/videos',
		} ).then( ( videos ) => {
			const videosOption = [];
			videos.forEach( function ( item ) {
				let postTitle = item.title;
				postTitle = postTitle.replace( /&#038;/g, '&' );
				postTitle = postTitle.replace( /&#8217;/g, "'" );
				postTitle = postTitle.replace( /&#8230;/g, '...' );
				postTitle = postTitle.replace( /&#8216;/g, "'" );
				postTitle = postTitle.replace( /&#8220;/g, '"' );
				postTitle = postTitle.replace( /&#8221;/g, '"' );
				postTitle = postTitle.replace( /&#8211;/g, '-' );
				postTitle = postTitle.replace( /&amp;/g, '&' );
				videosOption.push( {
					label: postTitle,
					value: item.id,
				} );
			} );

			setvideosList( videosOption );
		} );
	}, [] );

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newValue = arrayMove(
			selectedVideoIDs ? JSON.parse( selectedVideoIDs ) : [],
			oldIndex,
			newIndex
		);
		setAttributes( { selectedVideoIDs: JSON.stringify( newValue ) } );
	};

	initSlider();
	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
						<ToggleControl
							label={ __( 'Show Latest Videos', 'vaultrix' ) }
							checked={ videoSelectionToggle }
							onChange={ ( value ) =>
								setAttributes( { videoSelectionToggle: value } )
							}
						/>
						{ ! videoSelectionToggle && (
							<>
								<label htmlFor="videos">
									{ __( 'Select Videos', 'vaultrix' ) }
								</label>
								<SortableSelect
									onSortEnd={ onSortEnd }
									isFixed={ false }
									distance={ 4 }
									placeholder={ __(
										'Select Videos',
										'vaultrix'
									) }
									getHelperDimensions={ ( { node } ) =>
										node.getBoundingClientRect()
									}
									isMulti
									options={ videosList }
									value={
										selectedVideoIDs
											? JSON.parse( selectedVideoIDs )
											: []
									}
									onChange={ ( value ) =>
										setAttributes( {
											selectedVideoIDs:
												JSON.stringify( value ),
										} )
									}
									components={ {
										MultiValue: SortableMultiValue,
									} }
									closeMenuOnSelect={ false }
									className="components-base-control ant-select"
								/>
							</>
						) }
						<RangeControl
							label={ __(
								'Number of Videos to Display',
								'vaultrix'
							) }
							value={ numberOfPosts }
							onChange={ ( value ) =>
								setAttributes( { numberOfPosts: value } )
							}
							min={ 2 }
							max={ 8 }
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Slider Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Arrows Visibility', 'vaultrix' ) }
							checked={ displayArrows }
							onChange={ () =>
								setAttributes( {
									displayArrows: ! displayArrows,
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Dots Visibility', 'vaultrix' ) }
							checked={ displayDots }
							onChange={ () =>
								setAttributes( {
									displayDots: ! displayDots,
								} )
							}
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Overlay Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Enable Overlay', 'vaultrix' ) }
							checked={ enableOverlay }
							onChange={ ( value ) =>
								setAttributes( { enableOverlay: value } )
							}
						/>
						{ enableOverlay && (
							<>
								<RangeControl
									label={ __( 'Overlay Opacity', 'vaultrix' ) }
									value={ overlayOpacity }
									onChange={ ( value ) =>
										setAttributes( { overlayOpacity: value } )
									}
									min={ 0 }
									max={ 1 }
									step={ 0.01 }
								/>
								<PanelColorSettings
									colorSettings={ [
										{
											value: overlayColor,
											onChange: ( value ) => {
												setAttributes( {
													overlayColor: value,
												} );
											},
											label: __(
												'Overlay Color',
												'vaultrix'
											),
										},
									] }
									colors={ prjdpColors }
								/>
							</>
						) }
					</PanelBody>
				</div>
			</InspectorControls>
			<ServerSideRender
				block={ metadata.name }
				attributes={ {
					blockID,
					selectedVideoIDs,
					displayArrows,
					displayDots,
					numberOfPosts,
					videoSelectionToggle,
					enableOverlay,
					overlayColor,
					overlayOpacity,
				} }
			/>
		</>
	);
}
