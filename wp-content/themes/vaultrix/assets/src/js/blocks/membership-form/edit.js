/* eslint-disable no-unused-expressions */
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */
import {
	useBlockProps,
	InspectorControls,
	PanelColorSettings,
	MediaUpload,
	MediaUploadCheck,
	RichText,
} from '@wordpress/block-editor';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import {
	PanelBody,
	Button,
	SelectControl,
	Tooltip,
	ToggleControl,
} from '@wordpress/components';

import { prjdpColors, prjdpBtnStyles } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

export default function Edit( {
	attributes: {
		blockID,
		bgImage,
		rightImage,
		heading,
		description,
		bgColor,
		headingColor,
		descriptionColor,
		button,
		btnStyle,
		contentAlignment,
		showheading,
		showdesc,
	},
	setAttributes,
	clientId,
	className,
} ) {
	setAttributes( { blockID: `membership-form-block-unique-${ clientId }` } );
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
		'membership-form',
		contentAlignment
	);

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
	} );

	const btnClasses = classnames( btnStyle );
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
						<ToggleControl
							label={ __( 'Show Description', 'vaultrix' ) }
							checked={ showdesc }
							onChange={ ( value ) =>
								setAttributes( { showdesc: value } )
							}
						/>
						<SelectControl
							label={ __(
								'Select Button Style',
								'vaultrix'
							) }
							value={ btnStyle }
							options={ prjdpBtnStyles }
							onChange={ ( value ) =>
								setAttributes( { btnStyle: value } )
							}
						/>
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
						initialOpen={ true }
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
			<div { ...blockProps }>
				<div className="container" style={ blockStyle }>
					<div className="membership-form__left-section">
						{ showheading && (
							<RichText
								tagName="h3"
								placeholder={ __(
									'Enter Heading',
									'vaultrix'
								) }
								onChange={ ( value ) => {
									setAttributes( { heading: value } );
								} }
								value={ heading }
								className="membership-form__heading"
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
								className="membership-form__description"
								style={ descStyle }
							/>
						) }
						<RichText
							className={ btnClasses }
							tagName="p"
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
						/>
					</div>
					<div className="membership-form__right-section">
						<div className="image-preview image-controle-visible-hover">
							{ ! rightImage.url ? (
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
												rightImage: newImage,
											} );
										} }
										value={ rightImage.id }
										render={ ( { open } ) => (
											<div className="upload-wrap">
												<Tooltip
													text={ __(
														'Upload Image of size 310 x 385px',
														'vaultrix'
													) }
												>
													<Button
														onClick={ open }
														className="button media_and_text__upload_btn"
													>
														<i className="dashicons dashicons-upload upload-image"></i>
													</Button>
												</Tooltip>
											</div>
										) }
									/>
								</MediaUploadCheck>
							) : (
								<>
									<div className="image-preview">
										<img
											src={ rightImage.url }
											width={ rightImage.width }
											height={ rightImage.height }
											alt={ rightImage.alt }
										/>

										<div className="image-controls small-icons icon-center-fixed">
											<MediaUploadCheck>
												<MediaUpload
													title={ __(
														'Replace image',
														'vaultrix'
													) }
													value={ rightImage.id }
													onSelect={ ( image ) => {
														const newImage = {};
														newImage.id = image.id;
														newImage.url =
															image.url;
														newImage.alt =
															image.alt;
														newImage.width =
															image.width;
														newImage.height =
															image.height;
														setAttributes( {
															rightImage:
																newImage,
														} );
													} }
													allowedTypes={ [ 'image' ] }
													render={ ( { open } ) => (
														<Tooltip
															text={ __(
																'Edit Image',
																'vaultrix'
															) }
														>
															<i
																className="dashicons dashicons-edit edit-item"
																onClick={ open }
																aria-hidden="true"
															></i>
														</Tooltip>
													) }
												/>
												<Tooltip
													text={ __(
														'Remove Image',
														'vaultrix'
													) }
												>
													<i
														className="dashicons dashicons-no-alt remove-image"
														onClick={ () => {
															setAttributes( {
																rightImage: '',
															} );
														} }
														aria-hidden="true"
													></i>
												</Tooltip>
											</MediaUploadCheck>
										</div>
									</div>
								</>
							) }
						</div>
					</div>
				</div>
			</div>
		</>
	);
}
