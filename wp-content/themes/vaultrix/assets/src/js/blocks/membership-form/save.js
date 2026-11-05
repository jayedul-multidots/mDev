/* eslint-disable no-unused-expressions */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

// eslint-disable-next-line import/no-extraneous-dependencies
import classnames from 'classnames';

export default function save( props ) {
	const { attributes, className } = props;
	const {
		blockID,
		bgImage,
		rightImage,
		heading,
		description,
		bgColor,
		headingColor,
		descriptionColor,
		button,
		btnStyle,
		contentAlignment,
		showheading,
		showdesc,
	} = attributes;

	const classes = classnames( className, 'membership-form', contentAlignment );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const btnClasses = classnames( btnStyle );

	const blockStyle = {};
	bgColor && ( blockStyle.backgroundColor = bgColor );
	bgImage &&
		bgImage.url &&
		( blockStyle.backgroundImage = `url(${ bgImage.url })` );
	return (
		<div
			{ ...useBlockProps.save( {
				className: classes,
				id: blockID,
			} ) }
		>
			<div className="container" style={ blockStyle }>
				<div className="membership-form__left-section">
					{ showheading && heading &&(
						<RichText.Content
							tagName="h3"
							value={ heading }
							style={ headingStyle }
							className="membership-form__heading"
						/>
					)}
					{ showdesc && description && (
						<RichText.Content
							className="membership-form__description"
							tagName="p"
							value={ description }
							style={ descStyle }
						/>
					)}
					{ button  && (
						<RichText.Content
							tagName="p"
							className={ btnClasses }
							value={ button }
						/>
					)}
				</div>
				<div className="membership-form__right-section">
					{ rightImage.url && (
						<img
							src={ rightImage.url }
							width={ rightImage.width }
							height={ rightImage.height }
							alt={ rightImage.alt }
						/>
					)}
				</div>
			</div>
		</div>
	);
}
