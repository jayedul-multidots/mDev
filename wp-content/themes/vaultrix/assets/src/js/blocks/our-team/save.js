/* eslint-disable no-unused-expressions */
/**
 * React hook that is used to mark the block wrapper element.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/
 */
import { useBlockProps, RichText, InnerBlocks } from '@wordpress/block-editor';
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

	const classes = classnames( className, 'our-team' );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const ContentStyle = {};
	ContentColor && ( ContentStyle.color = ContentColor );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );

	const blockProps = useBlockProps.save( {
		className: classes,
		id: blockID,
		style: blockStyle,
	} );

	return (
		<div { ...blockProps }>
			<div className="container">
				{ ( ( showHeading && heading ) ||
					( Content && showContent ) ) && (
					<div className={ `our-team__head ${ contentAlignment }` }>
						{ heading && showHeading && (
							<RichText.Content
								tagName="h2"
								value={ heading }
								style={ headingStyle }
								className="our-team__heading"
							/>
						) }
						{ Content && showContent && (
							<RichText.Content
								tagName="p"
								value={ Content }
								style={ ContentStyle }
								className="our-team__content"
							/>
						) }
					</div>
				) }
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
