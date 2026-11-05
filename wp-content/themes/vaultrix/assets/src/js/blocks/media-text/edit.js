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
	RichText,
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
	Button,
	ToggleControl,
	Tooltip,
} from '@wordpress/components';

/**
 * React hook that is used to mark the packages element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-element/
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';
import './editor.scss';
import { prjdpColors } from '../common';
import { leftAlign, rightAlign, centerAlign } from '../icons';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId, className } = props;
	const {
		blockID,
		blockLayout,
		showHeading,
		showDesc,
		showImage,
		heading,
		description,
		mediaImage,
		headingColor,
		descColor,
		backgroundColor,
		contentAlignment,
	} = attributes;
	if ( ! blockID ) {
		setAttributes( { blockID: `media-text-block-${ clientId }` } );
	}
	const blockStyle = {};
	backgroundColor && ( blockStyle.backgroundColor = backgroundColor );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descColor && ( descStyle.color = descColor );

	const classes = classnames(
		className,
		'media-text-block',
		blockLayout,
		contentAlignment
	);

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
	} );

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
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/media-text-layout-1.png')`,
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
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/media-text-layout-2.png')`,
										backgroundSize: '100%',
										backgroundPosition: 'center',
									} }
								></div>
								<label htmlFor="layout-2">
									{ __( 'Layout 2', 'vaultrix' ) }
								</label>
							</Button>
							<Button
								className={
									'layout-3' === blockLayout
										? 'button active'
										: 'button'
								}
								onClick={ () => {
									setAttributes( {
										blockLayout: 'layout-3',
									} );
								} }
								value="layout-3"
							>
								<div
									style={ {
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/media-text-layout-3.png')`,
										backgroundSize: '100%',
										backgroundPosition: 'center',
									} }
								></div>
								<label htmlFor="layout-3">
									{ __( 'Layout 3', 'vaultrix' ) }
								</label>
							</Button>
							<Button
								className={
									'layout-4' === blockLayout
										? 'button active'
										: 'button'
								}
								onClick={ () => {
									setAttributes( {
										blockLayout: 'layout-4',
									} );
								} }
								value="layout-4"
							>
								<div
									style={ {
										backgroundImage: `url('/wp-content/themes/vaultrix/assets/library/img/media-text-layout-4.png')`,
										backgroundSize: '100%',
										backgroundPosition: 'center',
									} }
								></div>
								<label htmlFor="layout-4">
									{ __( 'Layout 4', 'vaultrix' ) }
								</label>
							</Button>
						</div>
					</PanelBody>
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Show Image', 'vaultrix' ) }
							checked={ showImage }
							onChange={ ( value ) =>
								setAttributes( { showImage: value } )
							}
						/>

						<ToggleControl
							label={ __( 'Show Heading', 'vaultrix' ) }
							checked={ showHeading }
							onChange={ ( value ) =>
								setAttributes( { showHeading: value } )
							}
						/>
						{ blockLayout !== 'layout-4' && (
							<ToggleControl
								label={ __(
									'Show Description',
									'vaultrix'
								) }
								checked={ showDesc }
								onChange={ ( value ) =>
									setAttributes( { showDesc: value } )
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
						title={ __( 'Color Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<PanelColorSettings
							colorSettings={ [
								{
									value: backgroundColor,
									onChange: ( newColor ) => {
										setAttributes( {
											backgroundColor: newColor,
										} );
									},
									label: __( 'Background Color' ),
								},
								{
									value: headingColor,
									onChange: ( newColor ) => {
										setAttributes( {
											headingColor: newColor,
										} );
									},
									label: __( 'Heading Color' ),
								},
								{
									value: descColor,
									onChange: ( newColor ) => {
										setAttributes( {
											descColor: newColor,
										} );
									},
									label: __( 'Description Color' ),
								},
							] }
							colors={ prjdpColors }
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<div { ...blockProps } style={ blockStyle }>
				<div className="container">
					<div className="media-text-block__col-wrapper">
						{ showHeading && (
							<RichText
								tagName="h2"
								value={ heading }
								className="media-text-block__heading"
								onChange={ ( value ) =>
									setAttributes( { heading: value } )
								}
								placeholder="Enter Heading"
								style={ headingStyle }
							/>
						) }
						{ blockLayout !== 'layout-2' && (
							<>
								{ showDesc && blockLayout !== 'layout-4' && (
									<RichText
										tagName="p"
										value={ description }
										className="media-text-block__desc"
										onChange={ ( value ) =>
											setAttributes( {
												description: value,
											} )
										}
										placeholder="Enter Description"
										style={ descStyle }
									/>
								) }
								{ showImage && (
									<div className="media-text-block__col-wrapper__col-media">
										<div className="media-text-block__image-wrapper image-preview image-controle-visible-hover">
											{ ! mediaImage.url ? (
												<MediaUploadCheck>
													<MediaUpload
														allowedTypes={ [
															'image',
														] }
														onSelect={ (
															image
														) => {
															const newImage = {};
															newImage.id =
																image.id;
															newImage.url =
																image.url;
															newImage.alt =
																image.alt;
															newImage.width =
																image.width;
															newImage.height =
																image.height;
															setAttributes( {
																mediaImage:
																	newImage,
															} );
														} }
														value={ mediaImage.id }
														render={ ( {
															open,
														} ) => (
															<div className="upload-wrap">
																{ blockLayout ===
																	'layout-1' && (
																	<Tooltip
																		text={ __(
																			'Upload Image of size 1120 x 631px',
																			'vaultrix'
																		) }
																	>
																		<Button
																			onClick={
																				open
																			}
																			className="button media_and_text__upload_btn"
																		>
																			<i className="dashicons dashicons-upload upload-image"></i>
																		</Button>
																	</Tooltip>
																) }
																{ blockLayout ===
																	'layout-2' && (
																	<Tooltip
																		text={ __(
																			'Upload Image of size 370 x 370px',
																			'vaultrix'
																		) }
																	>
																		<Button
																			onClick={
																				open
																			}
																			className="button media_and_text__upload_btn"
																		>
																			<i className="dashicons dashicons-upload upload-image"></i>
																		</Button>
																	</Tooltip>
																) }
																{ blockLayout ===
																	'layout-3' && (
																	<Tooltip
																		text={ __(
																			'Upload Image of size 560 x 560px',
																			'vaultrix'
																		) }
																	>
																		<Button
																			onClick={
																				open
																			}
																			className="button media_and_text__upload_btn"
																		>
																			<i className="dashicons dashicons-upload upload-image"></i>
																		</Button>
																	</Tooltip>
																) }
																{ blockLayout ===
																	'layout-4' && (
																	<Tooltip
																		text={ __(
																			'Upload Image of size 1110 x 556px',
																			'vaultrix'
																		) }
																	>
																		<Button
																			onClick={
																				open
																			}
																			className="button media_and_text__upload_btn"
																		>
																			<i className="dashicons dashicons-upload upload-image"></i>
																		</Button>
																	</Tooltip>
																) }
															</div>
														) }
													/>
												</MediaUploadCheck>
											) : (
												<>
													<div className="image-preview">
														<img
															src={
																mediaImage.url
															}
															width={
																mediaImage.width
															}
															height={
																mediaImage.height
															}
															alt={
																mediaImage.alt
															}
														/>

														<div className="image-controls small-icons icon-center-fixed">
															<MediaUploadCheck>
																<MediaUpload
																	title={ __(
																		'Replace image',
																		'vaultrix'
																	) }
																	value={
																		mediaImage.id
																	}
																	onSelect={ (
																		image
																	) => {
																		const newImage =
																			{};
																		newImage.id =
																			image.id;
																		newImage.url =
																			image.url;
																		newImage.alt =
																			image.alt;
																		newImage.width =
																			image.width;
																		newImage.height =
																			image.height;
																		setAttributes(
																			{
																				mediaImage:
																					newImage,
																			}
																		);
																	} }
																	allowedTypes={ [
																		'image',
																	] }
																	render={ ( {
																		open,
																	} ) => (
																		<Tooltip
																			text={ __(
																				'Edit Image',
																				'vaultrix'
																			) }
																		>
																			<i
																				className="dashicons dashicons-edit edit-item"
																				onClick={
																					open
																				}
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
																			setAttributes(
																				{
																					mediaImage:
																						'',
																				}
																			);
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
								) }
							</>
						) }
						{ blockLayout === 'layout-2' && (
							<div className="media-text-block__content">
								{ showImage && (
									<div
										className="media-text-block__image-wrapper image-preview image-controle-visible-hover"
									>
										{ ! mediaImage.url ? (
											<MediaUploadCheck>
												<MediaUpload
													allowedTypes={ [ 'image' ] }
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
															mediaImage:
																newImage,
														} );
													} }
													value={ mediaImage.id }
													render={ ( { open } ) => (
														<div className="upload-wrap">
															{ blockLayout ===
																'layout-1' && (
																<Tooltip
																	text={ __(
																		'Upload Image of size 1120 x 631px',
																		'vaultrix'
																	) }
																>
																	<Button
																		onClick={
																			open
																		}
																		className="button media_and_text__upload_btn"
																	>
																		<i className="dashicons dashicons-upload upload-image"></i>
																	</Button>
																</Tooltip>
															) }
															{ blockLayout ===
																'layout-2' && (
																<Tooltip
																	text={ __(
																		'Upload Image of size 370 x 370px',
																		'vaultrix'
																	) }
																>
																	<Button
																		onClick={
																			open
																		}
																		className="button media_and_text__upload_btn"
																	>
																		<i className="dashicons dashicons-upload upload-image"></i>
																	</Button>
																</Tooltip>
															) }
														</div>
													) }
												/>
											</MediaUploadCheck>
										) : (
											<>
												<div className="image-preview">
													<img
														src={ mediaImage.url }
														width={
															mediaImage.width
														}
														height={
															mediaImage.height
														}
														alt={ mediaImage.alt }
													/>

													<div className="image-controls small-icons icon-center-fixed">
														<MediaUploadCheck>
															<MediaUpload
																title={ __(
																	'Replace image',
																	'vaultrix'
																) }
																value={
																	mediaImage.id
																}
																onSelect={ (
																	image
																) => {
																	const newImage =
																		{};
																	newImage.id =
																		image.id;
																	newImage.url =
																		image.url;
																	newImage.alt =
																		image.alt;
																	newImage.width =
																		image.width;
																	newImage.height =
																		image.height;
																	setAttributes(
																		{
																			mediaImage:
																				newImage,
																		}
																	);
																} }
																allowedTypes={ [
																	'image',
																] }
																render={ ( {
																	open,
																} ) => (
																	<Tooltip
																		text={ __(
																			'Edit Image',
																			'vaultrix'
																		) }
																	>
																		<i
																			className="dashicons dashicons-edit edit-item"
																			onClick={
																				open
																			}
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
																		setAttributes(
																			{
																				mediaImage:
																					'',
																			}
																		);
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
								) }
								{ showDesc && (
									<RichText
										tagName="p"
										value={ description }
										className="media-text-block__desc"
										onChange={ ( value ) =>
											setAttributes( {
												description: value,
											} )
										}
										placeholder="Enter Description"
										style={ descStyle }
									/>
								) }
							</div>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
