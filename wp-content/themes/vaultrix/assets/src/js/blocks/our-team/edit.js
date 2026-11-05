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
	InnerBlocks,
	Inserter,
	PanelColorSettings,
} from '@wordpress/block-editor';

import { leftAlign, rightAlign, centerAlign } from '../icons';

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

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

import { prjdpColors } from '../common';
import './editor.scss';

function MyButtonBlockAppender( { rootClientId } ) {
	return (
		<Inserter
			rootClientId={ rootClientId }
			renderToggle={ ( { onToggle, disabled } ) => (
				<Button
					className="my-button-block-appender"
					onClick={ onToggle }
					disabled={ disabled }
				>
					<Tooltip text={ __( 'Add New Row', 'vaultrix' ) }>
						<i
							className="add-new-item dashicons dashicons-plus"
							aria-hidden="true"
						></i>
					</Tooltip>
				</Button>
			) }
		/>
	);
}

export default function Edit( props ) {
	const { attributes, setAttributes, clientId, className } = props;
	const {
		blockID,
		showHeading,
		showContent,
		heading,
		Content,
		contentAlignment,
		headingColor,
		ContentColor,
		bgColor,
	} = attributes;

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const ContentStyle = {};
	ContentColor && ( ContentStyle.color = ContentColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );

	if ( ! blockID ) {
		setAttributes( { blockID: `our-team-${ clientId }` } );
	}
	const classes = classnames( className, 'our-team' );

	const blockProps = useBlockProps( {
		className: classes,
		id: blockID,
		style: blockStyle,
	} );

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
							checked={ showContent }
							onChange={ ( value ) =>
								setAttributes( { showContent: value } )
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
									value: ContentColor,
									onChange: ( value ) => {
										setAttributes( {
											ContentColor: value,
										} );
									},
									label: __(
										'Content Color',
										'vaultrix'
									),
								},
							] }
							colors={ prjdpColors }
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<div
				{ ...blockProps }
				data-attributes={ JSON.stringify( attributes ) }
			>
				<div className="container">
					<div className={ `our-team__head ${ contentAlignment }` }>
						{ showHeading && (
							<RichText
								tagName="h2"
								placeholder={ __(
									'Enter Heading',
									'vaultrix'
								) }
								value={ heading }
								onChange={ ( value ) => {
									setAttributes( { heading: value } );
								} }
								style={ headingStyle }
								className="our-team__heading"
							/>
						) }

						{ showContent && (
							<RichText
								tagName="p"
								placeholder={ __(
									'Enter Description',
									'vaultrix'
								) }
								value={ Content }
								onChange={ ( value ) => {
									setAttributes( { Content: value } );
								} }
								style={ ContentStyle }
								className="our-team__content"
							/>
						) }
					</div>

					<InnerBlocks
						allowedBlocks={ [ 'vaultrix/our-team-inner' ] }
						template={ [ [ 'vaultrix/our-team-inner' ] ] }
						orientation="vertical"
						renderAppender={ () => (
							<MyButtonBlockAppender rootClientId={ clientId } />
						) }
					/>
				</div>
			</div>
		</>
	);
}
