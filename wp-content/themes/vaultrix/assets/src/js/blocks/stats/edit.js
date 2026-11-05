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
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';

import { PanelBody, Tooltip, ToggleControl } from '@wordpress/components';

import { leftAlign, rightAlign, centerAlign } from '../icons';

import { prjdpColors } from '../common';

export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		description,
		showHeading,
		showDesc,
		contentAlignment,
		headingColor,
		descriptionColor,
		statsNumberColor,
		statsDescColor,
		bgColor,
		listItems,
	} = attributes;
	setAttributes( { blockID: `stats-section-${ clientId }` } );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const statsNumberStyle = {};
	statsNumberColor && ( statsNumberStyle.color = statsNumberColor );

	const statsDescStyle = {};
	statsDescColor && ( statsDescStyle.color = statsDescColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );

	const blockProps = useBlockProps( {
		className: 'stats-section',
		id: blockID,
		style: blockStyle,
	} );

	const addNewItem = () => {
		let listItemsArr = listItems;
		const newObject = [
			...listItems,
			{
				statsNumber: '',
				statsDescription: '',
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

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
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
								{
									value: statsNumberColor,
									onChange: ( value ) => {
										setAttributes( {
											statsNumberColor: value,
										} );
									},
									label: __(
										'Stats Number Color',
										'vaultrix'
									),
								},
								{
									value: statsDescColor,
									onChange: ( value ) => {
										setAttributes( {
											statsDescColor: value,
										} );
									},
									label: __(
										'Stats Description Color',
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
					<div
						className={ `stats-section__heading-wrapper ${ contentAlignment }` }
					>
						{ showHeading && (
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
								className="stats-section__heading"
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
								className="stats-section__description"
								style={ descStyle }
							/>
						) }
					</div>
					<div className="stats-section__wrapper">
						{ listItems.map( ( data, index ) => {
							return (
								<div
									className="stats-section__item show-items-hover-wrap"
									key={ index }
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
																index - 1
															)
														}
													></span>
												</Tooltip>
											) }
											{ index + 1 < listItems.length && (
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
																index + 1
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
															true === toDelete
														) {
															const arrayCopy = [
																...listItems,
															];
															arrayCopy.splice(
																index,
																1
															);
															setAttributes( {
																listItems:
																	arrayCopy,
															} );
														}
													} }
												></i>
											</Tooltip>
										) }
									</div>
									<RichText
										tagName="h4"
										placeholder={ __(
											'Enter Stats Number',
											'vaultrix'
										) }
										className="stats-section__item-number"
										onChange={ ( value ) => {
											const arrayCopy = [ ...listItems ];
											arrayCopy[ index ].statsNumber =
												value;
											setAttributes( {
												listItems: arrayCopy,
											} );
										} }
										value={ data.statsNumber }
										style={ statsNumberStyle }
									/>
									<RichText
										tagName="p"
										placeholder={ __(
											'Enter Stats Description',
											'vaultrix'
										) }
										className="stats-section__item-description"
										onChange={ ( value ) => {
											const arrayCopy = [ ...listItems ];
											arrayCopy[
												index
											].statsDescription = value;
											setAttributes( {
												listItems: arrayCopy,
											} );
										} }
										value={ data.statsDescription }
										style={ statsDescStyle }
									/>
								</div>
							);
						} ) }
					</div>
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
				</div>
			</div>
		</>
	);
}
