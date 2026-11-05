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
	RangeControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';

import { prjdpColors } from '../common';
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

import './editor.scss';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		showHeading,
		showDesc,
		heading,
		description,
		displayAutoSlide,
		displayArrows,
		contentAlignment,
		selectedShowsIDs,
		numberOfPosts,
		bgColor,
		headingColor,
		descriptionColor,
	} = attributes;

	const [ showCategory, setShowCategory ] = useState( [] );

	useEffect( () => {
		setAttributes( {
			blockID: `show-card-slider-section-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	const initSlider = () => {
		jQuery( `#${ blockID } .show-card-slider-section__video-show-wrapper` )
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 3,
				slidesToScroll: 1,
				dots: false,
				arrows: displayArrows,
				autoplay: displayAutoSlide,
			} );
	};

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/show_categories',
		} ).then( ( showCategories ) => {
			const showCategoriesOption = [];
			showCategories.forEach( function ( item ) {
				showCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setTimeout(
				function () {
					setShowCategory( showCategoriesOption );
				}.bind( this ),
				600
			);
		} );
	}, [] );

	useEffect( () => {
		setTimeout( () => initSlider(), 1500 );
	}, [
		blockID,
		showHeading,
		showDesc,
		heading,
		description,
		displayAutoSlide,
		displayArrows,
		contentAlignment,
		selectedShowsIDs,
		numberOfPosts,
		bgColor,
		headingColor,
		descriptionColor,
	] );

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newValue = arrayMove(
			JSON.parse( selectedShowsIDs ),
			oldIndex,
			newIndex
		);
		setAttributes( { selectedShowsIDs: JSON.stringify( newValue ) } );
	};

	initSlider();

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
						<label htmlFor="Shows">
							{ __( 'Select Show', 'vaultrix' ) }
						</label>
						<SortableSelect
							onSortEnd={ onSortEnd }
							isFixed={ false }
							distance={ 4 }
							placeholder={ __( 'Select Show', 'vaultrix' ) }
							getHelperDimensions={ ( { node } ) =>
								node.getBoundingClientRect()
							}
							isMulti
							options={ showCategory }
							value={ JSON.parse( selectedShowsIDs ) }
							onChange={ ( value ) => {
								setAttributes( {
									selectedShowsIDs: JSON.stringify( value ),
								} );
							} }
							components={ {
								MultiValue: SortableMultiValue,
							} }
							closeMenuOnSelect={ false }
							className="components-base-control ant-select"
						/>
						<RangeControl
							label={ __(
								'Number of Shows to Display',
								'vaultrix'
							) }
							value={ numberOfPosts }
							onChange={ ( value ) =>
								setAttributes( { numberOfPosts: value } )
							}
							min={ 3 }
							max={ 15 }
						/>
						<ToggleControl
							label={ __( 'Show Heading', 'vaultrix' ) }
							checked={ showHeading }
							onChange={ ( value ) =>
								setAttributes( { showHeading: value } )
							}
						/>
						{ showHeading && (
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
						) }
						<ToggleControl
							label={ __( 'Show Description', 'vaultrix' ) }
							checked={ showDesc }
							onChange={ ( value ) =>
								setAttributes( { showDesc: value } )
							}
						/>
						{ showDesc && (
							<TextControl
								label={ __( 'Description', 'vaultrix' ) }
								placeholder={ __(
									'Enter Description',
									'vaultrix'
								) }
								value={ description }
								onChange={ ( value ) =>
									setAttributes( { description: value } )
								}
							/>
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
						title={ __( 'Slider Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Autoslide', 'vaultrix' ) }
							checked={ displayAutoSlide }
							onChange={ () =>
								setAttributes( {
									displayAutoSlide: ! displayAutoSlide,
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
									value: descriptionColor,
									onChange: ( value ) => {
										setAttributes( {
											descriptionColor: value,
										} );
									},
									label: __(
										'Description Color',
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
					showHeading,
					showDesc,
					heading,
					description,
					displayAutoSlide,
					displayArrows,
					contentAlignment,
					selectedShowsIDs,
					numberOfPosts,
					bgColor,
					headingColor,
					descriptionColor,
				} }
			/>
		</>
	);
}
