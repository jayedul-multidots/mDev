/* eslint-disable no-unused-expressions */
/* eslint-disable no-alert */

import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the components element.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/
 */
import {
	Button,
	PanelBody,
	SelectControl,
	Tooltip,
	ToggleControl,
} from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';

import classnames from 'classnames';
import Slider from 'react-slick';
import './editor.scss';
// Import css files
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';

import { prjdpColors, prjdpBtnStyles } from '../common';

import { useRef } from '@wordpress/element';

export default function Edit( props ) {
	const { attributes, setAttributes, className, clientId } = props;
	const {
		blockID,
		heading,
		showHeading,

		DescColor,
		TitleColor,
		headingColor,
		cardItems,
		button,
		showButton,
		btnStyle,
	} = attributes;

	setAttributes( { blockID: `coming-soon-block-${ clientId }` } );

	const moveItem = ( oldIndex, newIndex ) => {
		const arrayCopy = [ ...cardItems ];
		arrayCopy[ oldIndex ] = cardItems[ newIndex ];
		arrayCopy[ newIndex ] = cardItems[ oldIndex ];

		setAttributes( { cardItems: arrayCopy } );
	};

	const sliderRef = useRef( null );

	const addNewItem = () => {
		let slideItemsArr = cardItems;
		const newObject = [
			...cardItems,
			{
				title: '',
				desc: '',
				imageID: '',
				imageURL: '',
				imageAlt: '',
				imageWidth: '',
				imageHeight: '',
			},
		];
		slideItemsArr = newObject;
		setAttributes( {
			cardItems: slideItemsArr,
		} );
		setTimeout( () => {
			sliderRef.current.slickGoTo( slideItemsArr.length );
		}, 10 );
	};

	const settings = {
		accessibility: false,
		autoplay: true,
		arrows: true,
		dots: true,
		slidesToShow: 3,
		infinite: false,
	};

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const classes = classnames( className, 'coming-soon-block' );

	const TitleStyle = {};
	TitleColor && ( TitleStyle.color = TitleColor );

	const DescStyle = {};
	DescColor && ( DescStyle.color = DescColor );

	const btnClasses = classnames( btnStyle );

	const cardsSlides = cardItems.map( ( cardItem, index ) => {
		return (
			<div
				className="coming-soon-block-slider__item show-items-hover-wrap"
				key={ index }
			>
				<div className={ `item-action-wrap` }>
					<div className="move-item">
						{ 0 < index && (
							<Tooltip text={ __( 'Move Left', 'vaultrix' ) }>
								<span
									className="dashicons dashicons-arrow-left-alt move-left"
									aria-hidden="true"
									onClick={ () =>
										moveItem( index, index - 1 )
									}
								></span>
							</Tooltip>
						) }
						{ index + 1 < cardItems.length && (
							<Tooltip
								text={ __( 'Move Right', 'vaultrix' ) }
							>
								<span
									onKeyUp={ () => {} }
									role="button"
									tabIndex={ 0 }
									className="dashicons dashicons-arrow-right-alt move-right"
									onClick={ () =>
										moveItem( index, index + 1 )
									}
								></span>
							</Tooltip>
						) }
					</div>
					{ 1 < cardItems.length && (
						<Tooltip text={ __( 'Remove Item', 'vaultrix' ) }>
							<i
								onKeyUp={ () => {} }
								role="button"
								tabIndex={ 0 }
								className="remove-item dashicons dashicons-no-alt"
								onClick={ () => {
									const toDelete = confirm(
										__(
											'Are you sure you want to delete this item?'
										)
									);
									if ( true === toDelete ) {
										const arrayCopy = [ ...cardItems ];
										arrayCopy.splice( index, 1 );
										setAttributes( {
											cardItems: arrayCopy,
										} );
									}
								} }
							></i>
						</Tooltip>
					) }
				</div>
				<div className="coming-soon-block-slider__content">
					<div className="coming-soon-block-slider__bottom-wrap">
						{ cardItem.imageURL && (
							<div className="dp-block-control image-preview image-controle-visible-hover">
								<div className={ `image-controls small-icons` }>
									<MediaUploadCheck>
										<MediaUpload
											onSelect={ ( item ) => {
												const arrayCopy = [
													...cardItems,
												];
												arrayCopy[ index ].imageID =
													item.id;
												arrayCopy[ index ].imageURL =
													item.url;
												arrayCopy[ index ].imageAlt =
													item.alt;
												arrayCopy[ index ].imageWidth =
													item.width;
												arrayCopy[ index ].imageHeight =
													item.height;
												setAttributes( {
													cardItems: arrayCopy,
												} );
											} }
											allowedTypes={ [ 'image' ] }
											value={ cardItem.imageID }
											render={ ( { open } ) => {
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
															tabIndex={ 0 }
															className="dashicons dashicons-edit edit-image"
															onClick={ open }
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
											tabIndex={ 0 }
											className="dashicons dashicons-no-alt remove-image"
											onClick={ () => {
												const arrayCopy = [
													...cardItems,
												];
												arrayCopy[ index ].imageID = '';
												arrayCopy[ index ].imageURL =
													'';
												arrayCopy[ index ].imageAlt =
													'';
												arrayCopy[ index ].imageWidth =
													'';
												arrayCopy[ index ].imageHeight =
													'';
												setAttributes( {
													cardItems: arrayCopy,
												} );
											} }
										></i>
									</Tooltip>
								</div>

								<div className="coming-soon-block-slider__image">
									<img
										width={ cardItem.imageWidth }
										height={ cardItem.imageHeight }
										src={ cardItem.imageURL }
										alt={ cardItem.imageAlt }
									/>
								</div>
							</div>
						) }
						{ ! cardItem.imageURL && (
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( item ) => {
										const arrayCopy = [ ...cardItems ];
										arrayCopy[ index ].imageID = item.id;
										arrayCopy[ index ].imageURL = item.url;
										arrayCopy[ index ].imageAlt = item.alt;
										arrayCopy[ index ].imageWidth =
											item.width;
										arrayCopy[ index ].imageHeight =
											item.height;
										setAttributes( {
											cardItems: arrayCopy,
										} );
									} }
									type="image"
									value={ cardItem.imageURL }
									render={ ( { open } ) => (
										<div className="upload-wrap">
											<Button
												onClick={ open }
												className="button media_and_text__upload_btn"
											>
												<Tooltip
													text={ __(
														'Upload Image of 575x325px',
														'vaultrix'
													) }
												>
													<i className="dashicons dashicons-upload"></i>
												</Tooltip>
											</Button>
										</div>
									) }
								/>
							</MediaUploadCheck>
						) }
						{ cardItem.imageURL && (
							<div className="coming-soon-block-slider__details">
								<div className="coming-soon-block-slider__name">
									<RichText
										tagName="h3"
										placeholder={ __(
											'Enter Title',
											'vaultrix'
										) }
										className="coming-soon-block-slider__series-name"
										value={ cardItem.title }
										onChange={ ( value ) => {
											const arrayCopy = [ ...cardItems ];
											arrayCopy[ index ].title = value;
											setAttributes( {
												cardItems: arrayCopy,
											} );
										} }
										style={ TitleStyle }
									/>
									<RichText
										tagName="p"
										placeholder={ __(
											'Enter Desc',
											'vaultrix'
										) }
										className="coming-soon-block-slider__episode-name"
										value={ cardItem.desc }
										onChange={ ( value ) => {
											const arrayCopy = [ ...cardItems ];
											arrayCopy[ index ].desc = value;
											setAttributes( {
												cardItems: arrayCopy,
											} );
										} }
										style={ DescStyle }
									/>
								</div>
							</div>
						) }
					</div>
				</div>
			</div>
		);
	} );

	return (
		<>
			<InspectorControls>
				<div className="dp-block-sidebar prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Show Heading', 'vaultrix' ) }
							checked={ showHeading }
							onChange={ ( value ) =>
								setAttributes( { showHeading: value } )
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
									value: TitleColor,
									onChange: ( value ) => {
										setAttributes( {
											TitleColor: value,
										} );
									},
									label: __(
										'Card Title Color',
										'vaultrix'
									),
								},
								{
									value: DescColor,
									onChange: ( value ) => {
										setAttributes( {
											DescColor: value,
										} );
									},
									label: __(
										'Card Description Color',
										'vaultrix'
									),
								},
							] }
							colors={ prjdpColors }
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<div className={ classes } id={ blockID }>
				<div className="container">
					<div className="coming-soon-block__head">
						{ showHeading && (
							<RichText
								tagName="h2"
								placeholder={ __(
									'Enter Heading',
									'vaultrix'
								) }
								onChange={ ( value ) =>
									setAttributes( { heading: value } )
								}
								value={ heading }
								className="coming-soon-block__heading"
								style={ headingStyle }
							/>
						) }
					</div>

					<div className="coming-soon-block-slider">
						<Slider
							ref={ sliderRef }
							{ ...settings }
							className="coming-soon-block-slider-main"
						>
							{ cardsSlides }
						</Slider>
						{ cardItems.length < 3 && (
							<div
								onKeyUp={ () => {} }
								role="button"
								tabIndex={ 0 }
								className="add-item-wrap"
								onClick={ () => {
									addNewItem();
								} }
							>
								<Tooltip
									text={ __(
										'Add New Item',
										'vaultrix'
									) }
								>
									<i
										className="add-new-item dashicons dashicons-plus"
										aria-hidden="true"
									></i>
								</Tooltip>
							</div>
						) }
					</div>
				</div>

				{ showButton && (
					<div className="coming-soon-block__btn btn-wrap">
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
		</>
	);
}
