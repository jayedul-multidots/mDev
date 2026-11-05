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
	Button,
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
		blockLayout,
		heading,
		headingColor,
		description,
		btnText,
		descriptionColor,
		displayAutoslide,
		displayArrows,
		contentAlignment,
		showheading,
		showdesc,
		selectedSeriesIDs,
		numberOfPosts,
	} = attributes;

	const [ seriesSubCategory, setseriesSubCategory ] = useState( [] );

	useEffect( () => {
		setAttributes( {
			blockID: `series-card-slider-section-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	const initSlider = () => {
		jQuery(
			`#${ blockID } .series-card-slider-section__video-series-wrapper`
		)
			.not( '.slick-initialized' )
			.slick( {
				infinite: false,
				slidesToShow: 3,
				slidesToScroll: 1,
				dots: false,
				arrows: displayArrows,
				autoplay: displayAutoslide,
			} );
	};

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/series_categories',
		} ).then( ( seriesCategories ) => {
			const seriesCategoriesOption = [];
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
		setTimeout( () => initSlider(), 1500 );
	}, [
		blockID,
		blockLayout,
		heading,
		headingColor,
		description,
		btnText,
		descriptionColor,
		displayAutoslide,
		displayArrows,
		contentAlignment,
		showheading,
		showdesc,
		selectedSeriesIDs,
		numberOfPosts,
	] );

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newValue = arrayMove(
			JSON.parse( selectedSeriesIDs ),
			oldIndex,
			newIndex
		);
		setAttributes( { selectedSeriesIDs: JSON.stringify( newValue ) } );
	};

	initSlider();

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Layout', 'vaultrix' ) }
						initialOpen={ true }
					>
						<div className="block-style-group">
							<Button
								className={
									'layout-1' === blockLayout
										? 'button active'
										: 'button'
								}
								onClick={ () => {
									setAttributes( {
										blockLayout: 'layout-1',
									} );
								} }
								value="layout-1"
							>
								<div
									style={ {
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/series-card-slider-layout-1.jpeg')`,
										backgroundSize: '100%',
										backgroundPosition: 'center',
									} }
								></div>
								<label htmlFor="layout-1">
									{ __( 'Layout 1', 'vaultrix' ) }
								</label>
							</Button>
							<Button
								className={
									'layout-2' === blockLayout
										? 'button active'
										: 'button'
								}
								onClick={ () => {
									setAttributes( {
										blockLayout: 'layout-2',
									} );
								} }
								value="layout-2"
							>
								<div
									style={ {
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/series-card-slider-layout-2.jpeg')`,
										backgroundSize: '100%',
										backgroundPosition: 'center',
									} }
								></div>
								<label htmlFor="layout-2">
									{ __( 'Layout 2', 'vaultrix' ) }
								</label>
							</Button>
						</div>
					</PanelBody>
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<label htmlFor="Episodes">
							{ __( 'Select Series', 'vaultrix' ) }
						</label>
						<SortableSelect
							onSortEnd={ onSortEnd }
							isFixed={ false }
							distance={ 4 }
							placeholder={ __(
								'Select Series',
								'vaultrix'
							) }
							getHelperDimensions={ ( { node } ) =>
								node.getBoundingClientRect()
							}
							isMulti
							options={ seriesSubCategory }
							value={ JSON.parse( selectedSeriesIDs ) }
							onChange={ ( value ) =>
								setAttributes( {
									selectedSeriesIDs: JSON.stringify( value ),
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
								'Number of Series to Display',
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
							checked={ showheading }
							onChange={ ( value ) =>
								setAttributes( { showheading: value } )
							}
						/>
						{ showheading && (
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
							checked={ showdesc }
							onChange={ ( value ) =>
								setAttributes( { showdesc: value } )
							}
						/>
						{ showdesc && (
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
						{ 'layout-1' === blockLayout && (
							<TextControl
								label={ __( 'Button', 'vaultrix' ) }
								placeholder={ __(
									'Enter Button Text',
									'vaultrix'
								) }
								value={ btnText }
								onChange={ ( value ) =>
									setAttributes( { btnText: value } )
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
					blockLayout,
					heading,
					headingColor,
					description,
					btnText,
					descriptionColor,
					displayAutoslide,
					displayArrows,
					contentAlignment,
					showheading,
					showdesc,
					selectedSeriesIDs,
					numberOfPosts,
				} }
			/>
		</>
	);
}
