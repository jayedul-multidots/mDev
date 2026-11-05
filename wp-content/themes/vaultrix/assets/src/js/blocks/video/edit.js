/* eslint-disable no-unused-expressions */
/* eslint-disable no-shadow */
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import { PanelBody, ToggleControl } from '@wordpress/components';
import './editor.scss';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	InspectorControls,
	PanelColorSettings,
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

import { useEffect, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import Select from 'react-select';

import { prjdpColors } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';

import metadata from './block.json';

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

/**
 * Decode HTML entities in post titles.
 *
 * @param {string} title Post title.
 * @return {string} Decoded title.
 */
function decodePostTitle( title ) {
	return title
		.replace( /&#038;/g, '&' )
		.replace( /&#8217;/g, "'" )
		.replace( /&#8230;/g, '...' )
		.replace( /&#8216;/g, "'" )
		.replace( /&#8220;/g, '"' )
		.replace( /&#8221;/g, '"' )
		.replace( /&#8211;/g, '-' )
		.replace( /&amp;/g, '&' );
}

export default function Edit( props ) {
	const { attributes, setAttributes, clientId, className } = props;
	const {
		blockID,
		selectedVideoID,
		description,
		preHeading,
		heading,
		bgColor,
		headingColor,
		descriptionColor,
		contentAlignment,
		showPreheading,
		showheading,
		showdesc,
	} = attributes;

	const [ videosList, setVideosList ] = useState( [] );

	useEffect( () => {
		setAttributes( { blockID: `dp-video-${ clientId }` } );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		apiFetch( {
			path: '/wp/v2/deep_pockets_videos?per_page=100&orderby=title&order=asc&_fields=id,title',
		} ).then( ( videos ) => {
			const videosOption = videos.map( ( item ) => ( {
				label: decodePostTitle( item.title.rendered ),
				value: item.id,
			} ) );

			setVideosList( videosOption );
		} );
	}, [] );

	const classes = classnames( className, 'dp-video', contentAlignment );

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
	} );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const selectedVideo =
		videosList.find( ( video ) => video.value === selectedVideoID ) || null;

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
						<label htmlFor="video-select">
							{ __( 'Select Video', 'vaultrix' ) }
						</label>
						<Select
							inputId="video-select"
							placeholder={ __( 'Select Video', 'vaultrix' ) }
							options={ videosList }
							value={ selectedVideo }
							onChange={ ( option ) =>
								setAttributes( {
									selectedVideoID: option ? option.value : 0,
								} )
							}
							isClearable
							className="components-base-control ant-select"
						/>
						<ToggleControl
							label={ __( 'Show Preheading', 'vaultrix' ) }
							checked={ showPreheading }
							onChange={ ( value ) =>
								setAttributes( { showPreheading: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show Heading', 'vaultrix' ) }
							checked={ showheading }
							onChange={ ( value ) =>
								setAttributes( { showheading: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show Description', 'vaultrix' ) }
							checked={ showdesc }
							onChange={ ( value ) =>
								setAttributes( { showdesc: value } )
							}
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
			<div { ...blockProps } style={ blockStyle }>
				<div className="container">
					{ showPreheading && (
						<div className="dp-video__preheading">
							<RichText
								tagName="span"
								placeholder={ __(
									'Enter Preheading',
									'vaultrix'
								) }
								onChange={ ( value ) => {
									setAttributes( { preHeading: value } );
								} }
								value={ preHeading }
							/>
						</div>
					) }
					{ showheading && (
						<RichText
							tagName="h2"
							placeholder={ __(
								'Enter Heading',
								'vaultrix'
							) }
							onChange={ ( value ) => {
								setAttributes( { heading: value } );
							} }
							value={ heading }
							className="dp-video__heading"
							style={ headingStyle }
						/>
					) }
					{ showdesc && (
						<RichText
							tagName="p"
							placeholder={ __(
								'Enter Description',
								'vaultrix'
							) }
							onChange={ ( value ) => {
								setAttributes( { description: value } );
							} }
							value={ description }
							className="dp-video__description"
							style={ descStyle }
						/>
					) }
					{ selectedVideoID > 0 && (
						<ServerSideRender
							block={ metadata.name }
							attributes={ {
								selectedVideoID,
								editorVideoOnly: true,
							} }
						/>
					) }
				</div>
			</div>
		</>
	);
}
