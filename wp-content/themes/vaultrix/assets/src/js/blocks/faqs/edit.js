/* eslint-disable import/no-extraneous-dependencies */
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
import { leftAlign, rightAlign, centerAlign } from '../icons';

import apiFetch from '@wordpress/api-fetch';
import { prjdpColors } from '../common';
import './editor.scss';
import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';
const SortableSelect = SortableContainer( Select );

import metadata from './block.json';

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
		selectedFaqsIDs,
		showSeeAllLink,
		seeAllLinkText,
		seeAllLink,
		numberOfPosts,
		// bgImage,
		headingColor,
		questionColor,
		answerColor,
		showheading,
		contentAlignment,
	} = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `faqs-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	const [ faqsList, setFaqsList ] = useState( [] );

	useEffect( () => {
		apiFetch( {
			path: '/dp_api/request/all_faqs',
		} ).then( ( faqs ) => {
			const faqsOption = [];
			faqs.forEach( function ( item ) {
				faqsOption.push( {
					label: item.title,
					value: item.id,
				} );
			} );

			setTimeout(
				function () {
					setFaqsList( faqsOption );
				}.bind( this ),
				1000
			);
		} );
	}, [] );

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newValue = arrayMove(
			JSON.parse( selectedFaqsIDs ),
			oldIndex,
			newIndex
		);
		setAttributes( { selectedFaqsIDs: JSON.stringify( newValue ) } );
	};

	return (
		<>
			<InspectorControls>
				<div className="dp-block-sidebar prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
						<label htmlFor="faqs">
							{ __( 'Select FAQs', 'vaultrix' ) }
						</label>
						<SortableSelect
							onSortEnd={ onSortEnd }
							isFixed={ false }
							distance={ 4 }
							placeholder={ __( 'Select FAQs', 'vaultrix' ) }
							getHelperDimensions={ ( { node } ) =>
								node.getBoundingClientRect()
							}
							isMulti
							options={ faqsList }
							value={ JSON.parse( selectedFaqsIDs ) }
							onChange={ ( value ) =>
								setAttributes( {
									selectedFaqsIDs: JSON.stringify( value ),
								} )
							}
							components={ {
								MultiValue: SortableMultiValue,
							} }
							closeMenuOnSelect={ false }
							className="components-base-control ant-select"
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
							label={ __(
								'Show View More Link',
								'vaultrix'
							) }
							checked={ showSeeAllLink }
							onChange={ ( value ) =>
								setAttributes( { showSeeAllLink: value } )
							}
						/>
						{ showSeeAllLink && (
							<>
								<TextControl
									label={ __(
										'View More Link Text',
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
										'View More Link URL',
										'vaultrix'
									) }
									value={ seeAllLink }
									onChange={ ( value ) =>
										setAttributes( { seeAllLink: value } )
									}
								/>
							</>
						) }
						<RangeControl
							label={ __(
								'Number of FAQs to Display',
								'vaultrix'
							) }
							value={ numberOfPosts }
							onChange={ ( value ) =>
								setAttributes( { numberOfPosts: value } )
							}
							min={ 1 }
							max={ 15 }
						/>
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
									value: questionColor,
									onChange: ( value ) => {
										setAttributes( {
											questionColor: value,
										} );
									},
									label: __(
										'Question Color',
										'vaultrix'
									),
								},
								{
									value: answerColor,
									onChange: ( value ) => {
										setAttributes( {
											answerColor: value,
										} );
									},
									label: __( 'Answer Color', 'vaultrix' ),
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
					selectedFaqsIDs,
					showSeeAllLink,
					seeAllLinkText,
					seeAllLink,
					numberOfPosts,
					// bgImage,
					headingColor,
					questionColor,
					answerColor,
					showheading,
					contentAlignment,
				} }
			/>
		</>
	);
}
