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
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
	RangeControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import { prjdpColors } from '../common';

import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';
const SortableSelect = SortableContainer( Select );

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
		heading,
		seeAllLinkText,
		seeAllLink,
		showSeeAllLink,
		selectedCategory,
		selectedSubCategory,
		selectedVideoIDs,
		headingColor,
		bgColor,
		displayAutoslide,
		displayArrows,
		numberOfPosts,
		subCategoryCount,
	} = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `video-listing-slider-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	const [ videosCategoriesList, setvideosCategoriesList ] = useState( [] );

	const [ videoSubCategory, setvideoSubCategory ] = useState( [] );

	const [ videosList, setvideosList ] = useState( [] );

	const initSlider = () => {
		jQuery( `#${ blockID } .video-listing-slider__boxes` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 6,
				slidesToScroll: 1,
				dots: false,
				arrows: displayArrows,
				autoplay: displayAutoslide,
			} );
	};

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [
		blockID,
		heading,
		seeAllLinkText,
		seeAllLink,
		showSeeAllLink,
		selectedCategory,
		selectedSubCategory,
		selectedVideoIDs,
		headingColor,
		bgColor,
		displayAutoslide,
		displayArrows,
		numberOfPosts,
		subCategoryCount,
	] );

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

	useEffect( () => {
		if ( ! selectedCategory ) {
			setAttributes( { subCategoryCount: 0 } );
			setvideoSubCategory( [] );
			return;
		}

		wp.apiFetch( {
			path:
				'/dp_api/request/video_sub_categories?video_show_category=' +
				selectedCategory,
		} ).then( ( videoSubCategories ) => {
			setAttributes( { subCategoryCount: videoSubCategories.length } );
			const videosubCategoriesOption = [];
			videosubCategoriesOption.push( {
				label: __( 'Select Video Sub Category', 'vaultrix' ),
				value: '',
			} );
			videoSubCategories.forEach( function ( item ) {
				videosubCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setvideoSubCategory( videosubCategoriesOption );
		} );
	}, [ selectedCategory, setAttributes ] );

	useEffect( () => {
		if ( ! selectedCategory ) {
			setvideosList( [] );
			return;
		}

		const subCategoryParam = selectedSubCategory
			? `&video_sub_category=${ selectedSubCategory }`
			: '';

		apiFetch( {
			path: `/vaultrix/v1/video-listing-slider/videos?video_show_category=${ selectedCategory }${ subCategoryParam }`,
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
	}, [ selectedCategory, selectedSubCategory ] );

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
						<SelectControl
							label={ __( 'Select Video Show', 'vaultrix' ) }
							value={ selectedCategory }
							options={ videosCategoriesList }
							onChange={ ( value ) => {
								setAttributes( {
									selectedCategory: value,
								} );
								setAttributes( {
									selectedSubCategory: '',
								} );
								setAttributes( {
									selectedVideoIDs: null,
								} );
							} }
						/>
						{ subCategoryCount > 0 && (
							<SelectControl
								label={ __(
									'Select Video Sub Category',
									'vaultrix'
								) }
								value={ selectedSubCategory }
								options={ videoSubCategory }
								onChange={ ( value ) => {
									setAttributes( {
										selectedSubCategory: value,
									} );
									setAttributes( {
										selectedVideoIDs: null,
									} );
								} }
							/>
						) }
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
									selectedVideoIDs: JSON.stringify( value ),
								} )
							}
							components={ {
								MultiValue: SortableMultiValue,
							} }
							closeMenuOnSelect={ false }
							className="components-base-control ant-select"
						/>
						<RangeControl
							label={ __(
								'Number of Videos to Display',
								'vaultrix'
							) }
							value={ numberOfPosts }
							onChange={ ( value ) =>
								setAttributes( { numberOfPosts: value } )
							}
							min={ 7 }
							max={ 15 }
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
						title={ __( 'Slider Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Autoslide', 'vaultrix' ) }
							checked={ displayAutoslide }
							onChange={ () =>
								setAttributes( {
									displayAutoslide: ! displayAutoslide,
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Arrows Visibility', 'vaultrix' ) }
							checked={ displayArrows }
							onChange={ () =>
								setAttributes( {
									displayArrows: ! displayArrows,
								} )
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
					seeAllLinkText,
					seeAllLink,
					showSeeAllLink,
					selectedCategory,
					selectedSubCategory,
					selectedVideoIDs,
					headingColor,
					bgColor,
					displayAutoslide,
					displayArrows,
					numberOfPosts,
				} }
			/>
		</>
	);
}
