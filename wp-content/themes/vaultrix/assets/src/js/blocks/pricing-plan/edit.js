/* eslint-disable no-unused-expressions */
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
import {
	InspectorControls,
	PanelColorSettings,
	MediaUploadCheck,
	MediaUpload,
} from '@wordpress/block-editor';

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
} from '@wordpress/components';

import metadata from './block.json';

import { prjdpColors } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';

import './editor.scss';

export default function Edit( {
	attributes: {
		heading,
		description,
		buttonText,
		headingColor,
		descriptionColor,
		bgColor,
		contentAlignment,
		showheading,
		showdesc,
		bottomThumbnailImage,
	},
	setAttributes,
	className,
} ) {
	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );
	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
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
						<TextControl
							label={ __( 'Button Text', 'vaultrix' ) }
							placeholder={ __(
								'Enter Button Text',
								'vaultrix'
							) }
							value={ buttonText }
							onChange={ ( value ) =>
								setAttributes( { buttonText: value } )
							}
						/>
						<div className="setting-row">
							<label htmlFor="bottomThumbnailImage">
								{ __( 'Bottom Payment Image', 'vaultrix' ) }
							</label>
							<div>
								{ ! bottomThumbnailImage.url ? (
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
													bottomThumbnailImage:
														newImage,
												} );
											} }
											value={ bottomThumbnailImage.id }
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
												src={ bottomThumbnailImage.url }
												alt="Preview"
											/>
										</div>
										<MediaUploadCheck>
											<MediaUpload
												title={ __(
													'Replace image',
													'vaultrix'
												) }
												value={
													bottomThumbnailImage.id
												}
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
														bottomThumbnailImage:
															newImage,
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
														bottomThumbnailImage:
															'',
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
			<ServerSideRender
				block={ metadata.name }
				className={ className }
				attributes={ {
					heading,
					description,
					buttonText,
					headingColor,
					descriptionColor,
					bgColor,
					contentAlignment,
					showheading,
					showdesc,
					bottomThumbnailImage,
				} }
			/>
		</>
	);
}
