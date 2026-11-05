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
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';

import { useEffect } from '@wordpress/element';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import {
	PanelBody,
	Button,
	Tooltip,
	RangeControl,
} from '@wordpress/components';

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId, className } = props;
	const { blockID, listItems, columns } = attributes;

	const addNewItem = () => {
		let listItemsArr = listItems;
		const newObject = [
			...listItems,
			{
				title: '',
				subTitle: '',
				imageID: '',
				imageURL: '',
				imageAlt: '',
				imageWidth: '',
				imageHeight: '',
			},
		];
		listItemsArr = newObject;
		setAttributes( {
			listItems: listItemsArr,
		} );
	};

	useEffect( () => {
		if ( 0 === listItems.length ) {
			initListItems();
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	const initListItems = () => {
		setAttributes( {
			listItems: [
				...listItems,
				{
					title: '',
					subTitle: '',
					imageID: '',
					imageURL: '',
					imageAlt: '',
					imageWidth: '',
					imageHeight: '',
				},
			],
		} );
	};

	const moveItem = ( oldIndex, newIndex ) => {
		const arrayCopy = [ ...listItems ];
		arrayCopy[ oldIndex ] = listItems[ newIndex ];
		arrayCopy[ newIndex ] = listItems[ oldIndex ];

		setAttributes( { listItems: arrayCopy } );
	};

	if ( ! blockID ) {
		setAttributes( { blockID: `our-team-${ clientId }` } );
	}
	const classes = classnames(
		className,
		'our-team-inner',
		`has-${ columns }-columns`
	);

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
	} );

	return (
		<>
			<InspectorControls>
				<div className="dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
						<RangeControl
							label={ __( 'Columns', 'vaultrix' ) }
							min={ 3 }
							max={ 4 }
							onChange={ ( newColumns ) => {
								setAttributes( {
									columns: newColumns,
								} );
							} }
							value={ columns }
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<div { ...blockProps }>
				{ 0 < listItems.length && (
					<>
						{ 0 < listItems.length && (
							<div className={ `our-team__boxes` }>
								{ listItems.map( ( data, index ) => {
									return (
										<div
											key={ index }
											className="our-team__box show-items-hover-wrap"
										>
											<div
												className={ `item-action-wrap u-margin-b10` }
											>
												<div className="move-item">
													{ 0 < index && (
														<Tooltip
															text={ __(
																'Move Left',
																'vaultrix'
															) }
														>
															<span
																className="dashicons dashicons-arrow-left-alt move-left"
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
																'Move Right',
																'vaultrix'
															) }
														>
															<span
																onKeyUp={ () => {} }
																role="button"
																tabIndex={ 0 }
																className="dashicons dashicons-arrow-right-alt move-right"
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
															'Remove Item',
															'vaultrix'
														) }
													>
														<i
															className="remove-item dashicons dashicons-no-alt"
															onKeyUp={ () => {} }
															role="button"
															tabIndex={ 0 }
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
											<div
												className={ `our-team__image` }
											>
												{ data.imageURL && (
													<div className="dp-block-control image-preview image-controle-visible-hover">
														<div
															className={ `image-controls small-icons icon-center-fixed` }
														>
															<MediaUploadCheck>
																<MediaUpload
																	onSelect={ (
																		item
																	) => {
																		const arrayCopy =
																			[
																				...listItems,
																			];
																		arrayCopy[
																			index
																		].imageID =
																			item.id;
																		arrayCopy[
																			index
																		].imageURL =
																			item.url;
																		arrayCopy[
																			index
																		].imageAlt =
																			item.alt;
																		arrayCopy[
																			index
																		].imageWidth =
																			item.width;
																		arrayCopy[
																			index
																		].imageHeight =
																			item.height;
																		setAttributes(
																			{
																				listItems:
																					arrayCopy,
																			}
																		);
																	} }
																	allowedTypes={ [
																		'image',
																	] }
																	value={
																		data.imageID
																	}
																	render={ ( {
																		open,
																	} ) => {
																		return (
																			<Tooltip
																				text={ __(
																					'Edit Image',
																					'vaultrix'
																				) }
																			>
																				<i
																					onKeyUp={ () => {} }
																					role="button"
																					tabIndex={
																						0
																					}
																					className="dashicons dashicons-edit edit-image"
																					onClick={
																						open
																					}
																				></i>
																			</Tooltip>
																		);
																	} }
																/>
															</MediaUploadCheck>
															<Tooltip
																text={ __(
																	'Remove Image',
																	'vaultrix'
																) }
															>
																<i
																	onKeyUp={ () => {} }
																	role="button"
																	tabIndex={
																		0
																	}
																	className="dashicons dashicons-no-alt remove-image"
																	onClick={ () => {
																		const arrayCopy =
																			[
																				...listItems,
																			];
																		arrayCopy[
																			index
																		].imageID =
																			'';
																		arrayCopy[
																			index
																		].imageURL =
																			'';
																		arrayCopy[
																			index
																		].imageAlt =
																			'';
																		arrayCopy[
																			index
																		].imageWidth =
																			'';
																		arrayCopy[
																			index
																		].imageHeight =
																			'';
																		setAttributes(
																			{
																				listItems:
																					arrayCopy,
																			}
																		);
																	} }
																></i>
															</Tooltip>
														</div>
														<img
															width={
																data.imageWidth
															}
															height={
																data.imageHeight
															}
															src={
																data.imageURL
															}
															alt={
																data.imageAlt
															}
														/>
													</div>
												) }
												{ ! data.imageURL && (
													<MediaUploadCheck>
														<MediaUpload
															onSelect={ (
																item
															) => {
																const arrayCopy =
																	[
																		...listItems,
																	];
																arrayCopy[
																	index
																].imageID =
																	item.id;
																arrayCopy[
																	index
																].imageURL =
																	item.url;
																arrayCopy[
																	index
																].imageAlt =
																	item.alt;
																arrayCopy[
																	index
																].imageWidth =
																	item.width;
																arrayCopy[
																	index
																].imageHeight =
																	item.height;
																setAttributes( {
																	listItems:
																		arrayCopy,
																} );
															} }
															allowedTypes={ [
																'image',
															] }
															value={
																data.imageURL
															}
															render={ ( {
																open,
															} ) => (
																<div className="upload-wrap">
																	<Tooltip
																		text={ __(
																			'Upload Image of size 365 x 320px',
																			'vaultrix'
																		) }
																	>
																		<Button
																			onClick={
																				open
																			}
																			className="button media_and_text__upload_btn"
																		>
																			<i className="dashicons dashicons-upload"></i>
																		</Button>
																	</Tooltip>
																</div>
															) }
														/>
													</MediaUploadCheck>
												) }
											</div>
											<div
												className={ `our-team__box_content` }
											>
												<RichText
													tagName="h3"
													placeholder={ __(
														'Enter Member Name',
														'vaultrix'
													) }
													value={ data.title }
													onChange={ ( value ) => {
														const arrayCopy = [
															...listItems,
														];
														arrayCopy[
															index
														].title = value;
														setAttributes( {
															listItems:
																arrayCopy,
														} );
													} }
													className="our-team__title"
												/>
												<RichText
													tagName="p"
													placeholder={ __(
														'Enter Member Designation',
														'vaultrix'
													) }
													value={ data.subTitle }
													onChange={ ( value ) => {
														const arrayCopy = [
															...listItems,
														];
														arrayCopy[
															index
														].subTitle = value;
														setAttributes( {
															listItems:
																arrayCopy,
														} );
													} }
													className="our-team__subtitle"
												/>
											</div>
										</div>
									);
								} ) }
							</div>
						) }
					</>
				) }
				{ listItems.length < 5 && (
					<div
						onKeyUp={ () => {} }
						role="button"
						tabIndex={ 0 }
						className="add-item-wrap"
						onClick={ () => {
							addNewItem();
						} }
					>
						<Tooltip text={ __( 'Add New Item', 'vaultrix' ) }>
							<i
								className="add-new-item dashicons dashicons-plus"
								aria-hidden="true"
							></i>
						</Tooltip>
					</div>
				) }
			</div>
		</>
	);
}
