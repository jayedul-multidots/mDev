/**
 * Editor UI for mdev-gb/post-gallery: settings in the sidebar, live preview
 * rendered by the server (same PHP as the front end).
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	Disabled,
	FormTokenField,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from './block.json';
import {
	categoryIdsFromNames,
	categoryNamesFromIds,
	supportsCategories,
	toImageSizeOptions,
	toPostTypeOptions,
} from './lib/editor-options';
import { getSortOptions } from './lib/sort-options';

const HEADING_OPTIONS = [ 2, 3, 4, 5, 6 ].map( ( level ) => ( {
	/* translators: %d: heading level, e.g. 3 for <h3>. */
	label: __( 'Heading %d', 'mdev-gb' ).replace( '%d', String( level ) ),
	value: String( level ),
} ) );

export default function Edit( { attributes, setAttributes } ) {
	const {
		postType,
		categories,
		perPage,
		defaultSort,
		columns,
		showSearch,
		showSort,
		showExcerpt,
		showDate,
		headingLevel,
		imageSize,
	} = attributes;

	const { postTypes, terms, imageSizes } = useSelect(
		( select ) => ( {
			postTypes: select( coreStore ).getPostTypes( { per_page: 100 } ),
			terms: select( coreStore ).getEntityRecords(
				'taxonomy',
				'category',
				{
					per_page: 100,
					_fields: 'id,name',
					context: 'view',
				}
			),
			imageSizes: select( blockEditorStore ).getSettings().imageSizes,
		} ),
		[]
	);

	const hasCategories = supportsCategories( postTypes, postType );
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Query', 'mdev-gb' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Post type', 'mdev-gb' ) }
						value={ postType }
						options={ toPostTypeOptions( postTypes ) }
						onChange={ ( value ) =>
							setAttributes( { postType: value, categories: [] } )
						}
					/>
					{ hasCategories && (
						<FormTokenField
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Limit to categories', 'mdev-gb' ) }
							value={ categoryNamesFromIds( categories, terms ) }
							suggestions={ ( terms ?? [] ).map(
								( term ) => term.name
							) }
							onChange={ ( tokens ) =>
								setAttributes( {
									categories: categoryIdsFromNames(
										tokens,
										terms
									),
								} )
							}
						/>
					) }
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Posts per page', 'mdev-gb' ) }
						value={ perPage }
						min={ 1 }
						max={ 24 }
						onChange={ ( value ) =>
							setAttributes( { perPage: value } )
						}
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Default sort', 'mdev-gb' ) }
						value={ defaultSort }
						options={ getSortOptions() }
						onChange={ ( value ) =>
							setAttributes( { defaultSort: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Display', 'mdev-gb' ) }>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'mdev-gb' ) }
						value={ columns }
						min={ 1 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show search', 'mdev-gb' ) }
						checked={ showSearch }
						onChange={ ( value ) =>
							setAttributes( { showSearch: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show sort', 'mdev-gb' ) }
						checked={ showSort }
						onChange={ ( value ) =>
							setAttributes( { showSort: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show excerpt', 'mdev-gb' ) }
						checked={ showExcerpt }
						onChange={ ( value ) =>
							setAttributes( { showExcerpt: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show date', 'mdev-gb' ) }
						checked={ showDate }
						onChange={ ( value ) =>
							setAttributes( { showDate: value } )
						}
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Card heading level', 'mdev-gb' ) }
						value={ String( headingLevel ) }
						options={ HEADING_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: Number( value ) } )
						}
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Image size', 'mdev-gb' ) }
						value={ imageSize }
						options={ toImageSizeOptions( imageSizes ) }
						onChange={ ( value ) =>
							setAttributes( { imageSize: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block={ metadata.name }
						attributes={ attributes }
						skipBlockSupportAttributes
					/>
				</Disabled>
			</div>
		</>
	);
}
