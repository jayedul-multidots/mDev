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
	ToggleControl,
	TextControl,
	SelectControl,
	Tooltip,
} from '@wordpress/components';

import { prjdpBtnStyles, prjdpColors } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';
import './editor.scss';

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId, className } = props;
	const {
		blockID,
		bgImage,
		heading,
		description,
		showButton,
		button,
		headingColor,
		descriptionColor,
		bgColor,
		listItems,
		btnStyle,
		contentAlignment,
		showheading,
		showdesc,
	} = attributes;
	setAttributes( { blockID: `hero-banner-${ clientId }` } );
	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );
	bgImage &&
		bgImage.url &&
		( blockStyle.backgroundImage = `url(${ bgImage.url })` );

	const classes = classnames( className, 'hero-banner', contentAlignment );

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
		style: blockStyle,
	} );

	const addNewItem = () => {
		let listItemsArr = listItems;
		const newObject = [
			...listItems,
			{
				highlightedText: '',
			},
		];
		listItemsArr = newObject;
		setAttributes( {
			listItems: listItemsArr,
		} );
	};

	const moveItem = ( oldIndex, newIndex ) => {
		const arrayCopy = [ ...listItems ];

		arrayCopy[ oldIndex ] = listItems[ newIndex ];
		arrayCopy[ newIndex ] = listItems[ oldIndex ];

		setAttributes( {
			listItems: arrayCopy,
		} );
	};

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
						<ToggleControl
							label={ __( 'Show Button', 'vaultrix' ) }
							checked={ showButton }
							onChange={ ( value ) =>
								setAttributes( { showButton: value } )
							}
						/>
						{ showButton && (
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
						) }
						<PanelBody
							title={ __(
								'Highlighted Texts Settings',
								'vaultrix'
							) }
							initialOpen={ false }
						>
							{ listItems.map( ( data, index ) => {
								return (
									<>
										<PanelBody
											key={ index }
											title={ `Highlighted Text ${
												index + 1
											}` }
											initialOpen={ false }
										>
											<div className="highlighted-text-settings">
												<TextControl
													onChange={ ( value ) => {
														const arrayCopy = [
															...listItems,
														];
														arrayCopy[
															index
														].highlightedText = value;
														setAttributes( {
															listItems:
																arrayCopy,
														} );
													} }
													placeholder={ __(
														'Add Highlighted Text',
														'vaultrix'
													) }
													value={
														data.highlightedText
													}
												/>
												<div
													className={ `item-action-wrap small-icons` }
												>
													<div className="move-item">
														{ 0 < index && (
															<Tooltip
																text={ __(
																	'Move Up',
																	'vaultrix'
																) }
															>
																<span
																	className="dashicons dashicons-arrow-up-alt move-up"
																	aria-hidden="true"
																	onClick={ () =>
																		moveItem(
																			index,
																			index -
																				1
																		)
																	}
																></span>
															</Tooltip>
														) }
														{ index + 1 <
															listItems.length && (
															<Tooltip
																text={ __(
																	'Move Down',
																	'vaultrix'
																) }
															>
																<span
																	onKeyUp={ () => {} }
																	role="button"
																	tabIndex={
																		0
																	}
																	className="dashicons dashicons-arrow-down-alt move-Down"
																	onClick={ () =>
																		moveItem(
																			index,
																			index +
																				1
																		)
																	}
																></span>
															</Tooltip>
														) }
													</div>
													{ 1 < listItems.length && (
														<Tooltip
															text={ __(
																'Remove Text',
																'vaultrix'
															) }
														>
															<i
																onKeyUp={ () => {} }
																role="button"
																tabIndex={ 0 }
																className="remove-item dashicons dashicons-no-alt"
																onClick={ () => {
																	const toDelete =
																		// eslint-disable-next-line no-alert
																		confirm(
																			__(
																				'Are you sure you want to delete this item?',
																				'vaultrix'
																			)
																		);
																	if (
																		true ===
																		toDelete
																	) {
																		const arrayCopy =
																			[
																				...listItems,
																			];
																		arrayCopy.splice(
																			index,
																			1
																		);
																		setAttributes(
																			{
																				listItems:
																					arrayCopy,
																			}
																		);
																	}
																} }
															></i>
														</Tooltip>
													) }
												</div>
											</div>
										</PanelBody>
									</>
								);
							} ) }
							<div
								onKeyUp={ () => {} }
								role="button"
								tabIndex={ 0 }
								className="add-item-wrap-btn"
								onClick={ () => {
									addNewItem();
								} }
							>
								{ __( 'Add New Item', 'vaultrix' ) }
							</div>
						</PanelBody>
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
				<div className="container">
					<div className="hero-banner__wrapper">
						{ showheading && (
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
								className="hero-banner__heading"
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
								className="hero-banner__description"
								style={ descStyle }
							/>
						) }
						{ showButton && (
							<div className="btn-wrap">
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
						) }
					</div>
				</div>
			</div>
		</>
	);
}
