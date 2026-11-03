/**
 * Pure helpers turning core-data records into editor control options.
 */

/**
 * Viewable post types for the post type picker (attachments excluded).
 *
 * @param {Object[]|null|undefined} postTypes Records from core-data getPostTypes().
 * @return {Array<{label: string, value: string}>} Options, sorted by label.
 */
export function toPostTypeOptions( postTypes ) {
	const options = ( postTypes ?? [] )
		.filter(
			( type ) =>
				type?.viewable && type.slug && type.slug !== 'attachment'
		)
		.map( ( type ) => ( {
			label: type.labels?.singular_name || type.name || type.slug,
			value: type.slug,
		} ) )
		.sort( ( a, b ) => a.label.localeCompare( b.label ) );

	return options.length > 0 ? options : [ { label: 'Post', value: 'post' } ];
}

/**
 * Whether a post type uses the category taxonomy.
 *
 * @param {Object[]|null|undefined} postTypes Post type records.
 * @param {string}                  slug      Post type slug.
 * @return {boolean} Supports categories.
 */
export function supportsCategories( postTypes, slug ) {
	const type = ( postTypes ?? [] ).find( ( item ) => item?.slug === slug );
	return Boolean( type?.taxonomies?.includes( 'category' ) );
}

/**
 * Category names for the selected IDs (unknown IDs are skipped).
 *
 * @param {number[]}                               ids        Selected IDs.
 * @param {Array<{id: number, name: string}>|null} categories Category records.
 * @return {string[]} Names.
 */
export function categoryNamesFromIds( ids, categories ) {
	const byId = new Map(
		( categories ?? [] ).map( ( term ) => [ term.id, term.name ] )
	);
	return ( ids ?? [] )
		.map( ( id ) => byId.get( id ) )
		.filter( ( name ) => typeof name === 'string' );
}

/**
 * Category IDs for token names (case-insensitive; unknown names dropped,
 * duplicates removed, order kept).
 *
 * @param {Array<string|{value: string}>}          tokens     FormTokenField tokens.
 * @param {Array<{id: number, name: string}>|null} categories Category records.
 * @return {number[]} IDs.
 */
export function categoryIdsFromNames( tokens, categories ) {
	const byName = new Map(
		( categories ?? [] ).map( ( term ) => [
			term.name.toLowerCase(),
			term.id,
		] )
	);
	const ids = ( tokens ?? [] )
		.map( ( token ) =>
			typeof token === 'string' ? token : ( token?.value ?? '' )
		)
		.map( ( name ) => byName.get( name.trim().toLowerCase() ) )
		.filter( ( id ) => Number.isInteger( id ) );

	return [ ...new Set( ids ) ];
}

/**
 * Image size options from block editor settings.
 *
 * @param {Array<{slug: string, name: string}>|undefined} sizes Editor image sizes.
 * @return {Array<{label: string, value: string}>} Options.
 */
export function toImageSizeOptions( sizes ) {
	return ( sizes ?? [] )
		.filter( ( size ) => size?.slug )
		.map( ( size ) => ( {
			label: size.name || size.slug,
			value: size.slug,
		} ) );
}
