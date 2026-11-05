/* eslint-disable no-unused-expressions */
/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @param {Object} props
 * @return {WPElement} Element to render.
 */
export default function save( props ) {
	const { attributes, className } = props;
	const { blockID, listItems, columns } = attributes;

	const classes = classnames(
		className,
		'our-team-inner',
		`has-${ columns }-columns`
	);

	const blockProps = useBlockProps.save( {
		className: classes,
		id: blockID,
	} );

	return (
		<div { ...blockProps }>
			{ 0 < listItems.length && (
				<div className={ `our-team__boxes` }>
					{ listItems.map( ( data, index ) => {
						return (
							<div className="our-team__box" key={ index }>
								{ data.imageURL && (
									<div className={ `our-team__image` }>
										<img
											width={ data.imageWidth }
											height={ data.imageHeight }
											src={ data.imageURL }
											alt={ data.imageAlt }
										/>
									</div>
								) }
								<div className="our-team__box_content">
									<RichText.Content
										tagName="h3"
										value={ data.title }
										className="our-team__title"
									/>
									<RichText.Content
										tagName="p"
										value={ data.subTitle }
										className="our-team__subtitle"
									/>
								</div>
							</div>
						);
					} ) }
				</div>
			) }
		</div>
	);
}
