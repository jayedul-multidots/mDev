/* eslint-disable jsdoc/check-param-names */
/* eslint-disable jsdoc/require-param-type */

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

import { PanelBody, TextControl, SelectControl, RangeControl } from '@wordpress/components';

import { prjdpColors } from '../common';
/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';
import { useEffect, useState } from '@wordpress/element';
// import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param  root0
 * @param  root0.attributes
 * @param  root0.attributes.heading
 * @param  root0.setAttributes
 * @param  root0.className
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 * @return {WPElement} Element to render.
 */
export default function Edit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const {
		blockID,
		heading,
		headingColor,
		bgColor,
		selectedCategory,
		selectedSubCategory,
		subCategoryCount,
		numberOfPosts,
	} = attributes;

	const [ videosCategoriesList, setvideosCategoriesList ] = useState( [] );

	const [ videoSubCategory, setvideoSubCategory ] = useState( [] );

	useEffect( () => {
		setAttributes( {
			blockID: `recently-added-videos-${ clientId }`,
		} );
	}, [ clientId, setAttributes ] );

	useEffect( () => {
		wp.apiFetch( {
			path: '/dp_api/request/video_categories',
		} ).then( ( videoCategories ) => {
			const videoCategoriesOption = [];
			videoCategoriesOption.push( {
				label: __( 'Select Video Show', 'vaultrix' ),
				value: '',
			} );
			videoCategories.forEach( function ( item ) {
				videoCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setTimeout(
				function () {
					setvideosCategoriesList( videoCategoriesOption );
				}.bind( this ),
				600
			);
		} );
	}, [] );

	useEffect( () => {
		wp.apiFetch( {
			path:
				'/dp_api/request/video_sub_categories?video_show_category=' +
				selectedCategory,
		} ).then( ( videoSubCategories ) => {
			setAttributes( { subCategoryCount: videoSubCategories.length } );

			const videosubCategoriesOption = [];
			videosubCategoriesOption.push( {
				label: __( 'Select Video Sub Category', 'vaultrix' ),
				value: '',
			} );
			videoSubCategories.forEach( function ( item ) {
				videosubCategoriesOption.push( {
					label: item.name,
					value: item.slug,
				} );
			} );

			setTimeout(
				function () {
					setvideoSubCategory( videosubCategoriesOption );
				}.bind( this ),
				600
			);
		} );
	}, [ selectedCategory ] );

	return (
		<>
			<InspectorControls>
				<div className="prj-dp-block-sidebar">
					<PanelBody
						title={ __( 'Settings', 'vaultrix' ) }
						initialOpen={ true }
					>
						<SelectControl
							label={ __( 'Select Video Show', 'vaultrix' ) }
							value={ selectedCategory }
							options={ videosCategoriesList }
							onChange={ ( value ) => {
								setAttributes( {
									selectedCategory: value,
								} );
								setAttributes( {
									selectedSubCategory: '',
								} );
							} }
						/>
						{ subCategoryCount > 0 && (
							<SelectControl
								label={ __(
									'Select Video Sub Category',
									'vaultrix'
								) }
								value={ selectedSubCategory }
								options={ videoSubCategory }
								onChange={ ( value ) => {
									setAttributes( {
										selectedSubCategory: value,
									} );
								} }
							/>
						) }
						{ /* 2026-07-01: Controls videos per page in the grid (passed as posts_per_page). */ }
						<RangeControl
							label={ __( 'Number of Posts', 'vaultrix' ) }
							value={ numberOfPosts }
							onChange={ ( value ) =>
								setAttributes( { numberOfPosts: value } )
							}
							min={ 1 }
							max={ 24 }
						/>
						<p className="components-base-control__help">
							{ __(
								'The number you set here is the number of videos shown per page.',
								'vaultrix'
							) }
						</p>
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
				attributes={ {
					blockID,
					heading,
					headingColor,
					bgColor,
					selectedCategory,
					selectedSubCategory,
					numberOfPosts,
				} }
			/>
		</>
	);
}
