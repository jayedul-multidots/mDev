/* eslint-disable jsdoc/check-param-names */
/* eslint-disable jsdoc/require-param */
/* eslint-disable jsdoc/require-param-type */
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
import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';
import { prjdpColors } from '../common';
import './editor.scss';
import { leftAlign, rightAlign, centerAlign } from '../icons';
const SortableSelect = SortableContainer( Select );

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

import metadata from './block.json';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param  root0
 * @param  root0.attributes
 * @param  root0.attributes.heading
 * @param  root0.setAttributes
 * @param  root0.className
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 * @return {WPElement} Element to render.
 */
export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		highlightedText,
		showButton,
		btnText,
		btnLink,
		selectedCategory,
		selectedEpisodeIDs,
		headingColor,
		bgColor,
		contentAlignment,
		numberOfPosts,
	} = attributes;

	const [ seriesSubCategory, setseriesSubCategory ] = useState( [] );
	const [ seriesEpisodesList, setseriesEpisodesList ] = useState( [] );
	useEffect( () => {
		setAttributes( {
			blockID: `episodes-listing-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/series_categories',
		} ).then( ( seriesCategories ) => {
			const seriesCategoriesOption = [];
			seriesCategoriesOption.push( {
				label: __( 'Select Series', 'vaultrix' ),
				value: '',
			} );
			seriesCategories.forEach( function ( item ) {
				seriesCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setTimeout(
				function () {
					setseriesSubCategory( seriesCategoriesOption );
				}.bind( this ),
				600
			);
		} );
	}, [] );

	useEffect( () => {
		apiFetch( {
			path: '/dp_api/request/series_episodes?series=' + selectedCategory,
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

			setTimeout(
				function () {
					setseriesEpisodesList( videosOption );
				}.bind( this ),
				1000
			);
		} );
	}, [ selectedCategory ] );

	const initSlider = () => {
		// if ( jQuery( `#${ blockID } .episodes-listing__box` ).length > 4 ) {
		jQuery( `#${ blockID } .episodes-listing__boxes` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 3,
				slidesToScroll: 1,
				dots: false,
				arrows: true,
				autoplay: true,
			} );
		// }
	};

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [
		blockID,
		heading,
		highlightedText,
		showButton,
		btnText,
		btnLink,
		selectedCategory,
		selectedEpisodeIDs,
		headingColor,
		bgColor,
		contentAlignment,
		numberOfPosts,
	] );

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newValue = arrayMove(
			JSON.parse( selectedEpisodeIDs ),
			oldIndex,
			newIndex
		);
		setAttributes( { selectedEpisodeIDs: JSON.stringify( newValue ) } );
	};

	initSlider();

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
						<SelectControl
							label={ __( 'Select Series', 'vaultrix' ) }
							value={ selectedCategory }
							options={ seriesSubCategory }
							onChange={ ( value ) => {
								setAttributes( {
									selectedCategory: value,
								} );
								setAttributes( {
									selectedEpisodeIDs: null,
								} );
							} }
						/>
						<label htmlFor="Episodes">
							{ __( 'Select Episodes', 'vaultrix' ) }
						</label>
						<SortableSelect
							onSortEnd={ onSortEnd }
							isFixed={ false }
							distance={ 4 }
							placeholder={ __(
								'Select Episodes',
								'vaultrix'
							) }
							getHelperDimensions={ ( { node } ) =>
								node.getBoundingClientRect()
							}
							isMulti
							options={ seriesEpisodesList }
							value={ JSON.parse( selectedEpisodeIDs ) }
							onChange={ ( value ) =>
								setAttributes( {
									selectedEpisodeIDs: JSON.stringify( value ),
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
							min={ 4 }
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
						<TextControl
							label={ __( 'Highlighted Text', 'vaultrix' ) }
							placeholder={ __(
								'Enter Highlighted Text',
								'vaultrix'
							) }
							value={ highlightedText }
							onChange={ ( value ) =>
								setAttributes( { highlightedText: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show Button', 'vaultrix' ) }
							checked={ showButton }
							onChange={ ( value ) =>
								setAttributes( { showButton: value } )
							}
						/>
						{ showButton && (
							<>
								<TextControl
									label={ __(
										'Button Text',
										'vaultrix'
									) }
									value={ btnText }
									onChange={ ( value ) =>
										setAttributes( {
											btnText: value,
										} )
									}
								/>
								<TextControl
									label={ __(
										'Button Link URL',
										'vaultrix'
									) }
									value={ btnLink }
									onChange={ ( value ) =>
										setAttributes( { btnLink: value } )
									}
								/>
							</>
						) }
					</PanelBody>
					<PanelBody
						title={ __( 'Text Alignment', 'vaultrix' ) }
						initialOpen={ false }
					>
						<div className="inspector-field-alignment inspector-field inspector-responsive">
							<div className="inspector-field-button-list inspector-field-button-list-fluid">
								<button
									className={ `inspector-button ${
										'left' === contentAlignment
											? 'active'
											: ''
									}` }
									onClick={ () =>
										setAttributes( {
											contentAlignment: 'left',
										} )
									}
								>
									{ leftAlign }
								</button>
								<button
									className={ `inspector-button ${
										'center' === contentAlignment
											? 'active'
											: ''
									}` }
									onClick={ () =>
										setAttributes( {
											contentAlignment: 'center',
										} )
									}
								>
									{ centerAlign }
								</button>
								<button
									className={ `inspector-button ${
										'right' === contentAlignment
											? 'active'
											: ''
									}` }
									onClick={ () =>
										setAttributes( {
											contentAlignment: 'right',
										} )
									}
								>
									{ rightAlign }
								</button>
							</div>
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
					highlightedText,
					showButton,
					btnText,
					btnLink,
					selectedCategory,
					selectedEpisodeIDs,
					headingColor,
					bgColor,
					contentAlignment,
					numberOfPosts,
				} }
			/>
		</>
	);
}
