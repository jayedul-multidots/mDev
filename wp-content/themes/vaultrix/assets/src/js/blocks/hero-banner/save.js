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
		heading,
		description,
		showButton,
		button,
		headingColor,
		descriptionColor,
		bgColor,
		listItems,
		btnStyle,
		contentAlignment,
		showheading,
		showdesc,
	} = attributes;

	const classes = classnames( className, 'hero-banner', contentAlignment );

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
				style: blockStyle,
			} ) }
		>
			<div className="container">
				<div className="hero-banner__wrapper">
					<h1 className="hero-banner__heading">
						{ heading && showheading && (
							<RichText.Content
								tagName="span"
								value={ heading }
								style={ headingStyle }
							/>
							) }

						{ showheading && ( <span className="highlighted-text"></span> )}
					</h1>
					{ 0 < listItems.length && (
						<div className="hero-banner__highlight-text-list">
							{ listItems.map( ( data ) => {
								return (
									<>
										{ data.highlightedText && (
											<RichText.Content
												tagName="span"
												className="hero-banner__highlighted-text"
												value={ data.highlightedText }
											/>
										) }
									</>
								);
							} ) }
						</div>
					) }
					{ description && showdesc && (
						<RichText.Content
							className="hero-banner__description"
							tagName="p"
							value={ description }
							style={ descStyle }
						/>
					) }
					{ showButton && button && (
						<div className="btn-wrap">
							<RichText.Content
								tagName="p"
								className={ btnClasses }
								value={ button }
							/>
						</div>
					) }
				</div>
			</div>
		</div>
	);
}
