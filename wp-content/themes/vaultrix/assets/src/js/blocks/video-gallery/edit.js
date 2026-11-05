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
import {
	InspectorControls,
	PanelColorSettings,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { prjdpColors } from '../common';
import './editor.scss';
import metadata from './block.json';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		description,
		categoryTitle,
		categoryPreviewTitle,
		headingColor,
		descriptionColor,
		bgColor,
		bgImage,
	} = attributes;
	useEffect( () => {
		setAttributes( {
			blockID: `faqs-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody title={ __( 'Settings', 'vaultrix' ) }>
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
						<TextareaControl
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
						<TextControl
							label={ __( 'Category Title', 'vaultrix' ) }
							placeholder={ __(
								'Enter Category Title',
								'vaultrix'
							) }
							value={ categoryTitle }
							onChange={ ( value ) =>
								setAttributes( { categoryTitle: value } )
							}
						/>
						<TextControl
							label={ __(
								'Category Preview Title',
								'vaultrix'
							) }
							placeholder={ __(
								'Enter Category Preview Title',
								'vaultrix'
							) }
							value={ categoryPreviewTitle }
							onChange={ ( value ) =>
								setAttributes( { categoryPreviewTitle: value } )
							}
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Background Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<div className="setting-row">
							<label htmlFor="background-image">
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
					heading,
					description,
					categoryTitle,
					categoryPreviewTitle,
					headingColor,
					descriptionColor,
					bgColor,
					bgImage,
				} }
			/>
		</>
	);
}
