/* eslint-disable no-unused-expressions */
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';

import {
	Button,
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import Select from 'react-select';

import { prjdpColors } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';
import './editor.scss';

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
		bgImage,
		showPreHeading,
		showHeading,
		showDesc,
		showButton,
		preHeading,
		heading,
		description,
		button,
		selectedVideoID,
		bgColor,
		preHeadingColor,
		headingColor,
		descriptionColor,
		contentAlignment,
	} = attributes;

	const [ videosList, setVideosList ] = useState( [] );

	useEffect( () => {
		setAttributes( { blockID: `video-category-hero-banner-${ clientId }` } );
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

	const preHeadingStyle = {};
	preHeadingColor && ( preHeadingStyle.color = preHeadingColor );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );
	bgImage &&
		bgImage.url &&
		( blockStyle.backgroundImage = `url(${ bgImage.url })` );

	const classes = classnames(
		className,
		'video-category-hero-banner',
		contentAlignment
	);

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
		style: blockStyle,
	} );

	const selectedVideo =
		videosList.find( ( video ) => video.value === selectedVideoID ) ||
		null;

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
						<ToggleControl
							label={ __( 'Show Preheading', 'vaultrix' ) }
							checked={ showPreHeading }
							onChange={ ( value ) =>
								setAttributes( { showPreHeading: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show Heading', 'vaultrix' ) }
							checked={ showHeading }
							onChange={ ( value ) =>
								setAttributes( { showHeading: value } )
							}
						/>
						<ToggleControl
							label={ __( 'Show Description', 'vaultrix' ) }
							checked={ showDesc }
							onChange={ ( value ) =>
								setAttributes( { showDesc: value } )
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
								<label htmlFor="video-select">
									{ __( 'Select Video', 'vaultrix' ) }
								</label>
								<Select
									inputId="video-select"
									placeholder={ __(
										'Select Video',
										'vaultrix'
									) }
									options={ videosList }
									value={ selectedVideo }
									onChange={ ( option ) =>
										setAttributes( {
											selectedVideoID: option
												? option.value
												: 0,
										} )
									}
									isClearable
									className="components-base-control ant-select"
								/>
								<TextControl
									label={ __( 'Button Text', 'vaultrix' ) }
									value={ button }
									onChange={ ( value ) =>
										setAttributes( { button: value } )
									}
									placeholder={ __(
										'Enter Button Text',
										'vaultrix'
									) }
								/>
							</>
						) }
					</PanelBody>
					<PanelBody
						title={ __( 'Content Alignment', 'vaultrix' ) }
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
						title={ __( 'Background Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<div className="setting-row">
							<label htmlFor="bgImage">
								{ __( 'Background Image', 'vaultrix' ) }
							</label>
							<div>
								{ ! bgImage.url ? (
									<MediaUploadCheck>
										<MediaUpload
											allowedTypes={ [ 'image' ] }
											onSelect={ ( image ) => {
												const newImage = {};
												newImage.id = image.id;
												newImage.url = image.url;
												newImage.alt = image.alt;
												newImage.width = image.width;
												newImage.height = image.height;
												setAttributes( {
													bgImage: newImage,
												} );
											} }
											value={ bgImage.id }
											render={ ( { open } ) => (
												<Button
													className="button"
													onClick={ open }
												>
													{ __(
														'Select Image',
														'vaultrix'
													) }
												</Button>
											) }
										/>
									</MediaUploadCheck>
								) : (
									<>
										<div className="image-preview">
											<img
												src={ bgImage.url }
												alt="Preview"
											/>
										</div>
										<MediaUploadCheck>
											<MediaUpload
												title={ __(
													'Replace image',
													'vaultrix'
												) }
												value={ bgImage.id }
												onSelect={ ( image ) => {
													const newImage = {};
													newImage.id = image.id;
													newImage.url = image.url;
													newImage.alt = image.alt;
													newImage.width =
														image.width;
													newImage.height =
														image.height;
													setAttributes( {
														bgImage: newImage,
													} );
												} }
												allowedTypes={ [ 'image' ] }
												render={ ( { open } ) => (
													<Button
														className="button"
														onClick={ open }
													>
														{ __(
															'Replace image',
															'vaultrix'
														) }
													</Button>
												) }
											/>
											<Button
												className="is-link is-destructive"
												onClick={ () => {
													setAttributes( {
														bgImage: '',
													} );
												} }
											>
												{ __(
													'Remove image',
													'vaultrix'
												) }
											</Button>
										</MediaUploadCheck>
									</>
								) }
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
									value: preHeadingColor,
									onChange: ( value ) => {
										setAttributes( {
											preHeadingColor: value,
										} );
									},
									label: __(
										'Preheading Color',
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
			<div { ...blockProps }>
				<div className="container">
					<div className="video-category-hero-banner__wrapper">
						{ showPreHeading && (
							<RichText
								tagName="h1"
								placeholder={ __(
									'Enter Preheading',
									'vaultrix'
								) }
								onChange={ ( value ) => {
									setAttributes( { preHeading: value } );
								} }
								value={ preHeading }
								className="video-category-hero-banner__preheading"
								style={ preHeadingStyle }
							/>
						) }
						{ showHeading && (
							<RichText
								tagName="h1"
								placeholder={ __(
									'Enter Heading',
									'vaultrix'
								) }
								onChange={ ( value ) => {
									setAttributes( { heading: value } );
								} }
								value={ heading }
								className="video-category-hero-banner__heading"
								style={ headingStyle }
							/>
						) }
						{ showDesc && (
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
								className="video-category-hero-banner__description"
								style={ descStyle }
							/>
						) }
						{ showButton && (
							<div className="btn-wrap">
								<div className="btn-main">
									<span
										className="play-icon"
										aria-hidden="true"
									></span>
									<RichText
										tagName="p"
										className="video-category-hero-banner__button-text"
										placeholder={ __(
											'Enter Button Text',
											'vaultrix'
										) }
										value={ button }
										onChange={ ( value ) => {
											setAttributes( {
												button: value,
											} );
										} }
										allowedFormats={ [] }
									/>
								</div>
							</div>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
