/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable no-unused-expressions */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( props ) {
	const { attributes, className } = props;
	const {
		blockID,
		blockLayout,
		showHeading,
		showDesc,
		showImage,
		heading,
		description,
		mediaImage,
		headingColor,
		descColor,
		backgroundColor,
		contentAlignment,
	} = attributes;

	const classes = classnames(
		className,
		'media-text-block',
		blockLayout,
		contentAlignment
	);

	const blockStyle = {};
	backgroundColor && ( blockStyle.backgroundColor = backgroundColor );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descColor && ( descStyle.color = descColor );

	return (
		<div
			{ ...useBlockProps.save( {
				className: classes,
				id: blockID,
				style: blockStyle,
			} ) }
		>
			<div className="container">
				<div className="media-text-block__col-wrapper">
					{ showHeading && heading && (
						<RichText.Content
							tagName="h2"
							value={ heading }
							className="media-text-block__heading"
							style={ headingStyle }
						/>
					) }
					{ blockLayout !== 'layout-2' && (
						<>
							{ blockLayout !== 'layout-4' &&
								showDesc &&
								description && (
									<RichText.Content
										tagName="p"
										value={ description }
										className="media-text-block__desc"
										style={ descStyle }
									/>
								) }
							{ showImage && mediaImage && (
								<div className="media-text-block__image-wrapper">
									<img
										src={ mediaImage.url }
										width={ mediaImage.width }
										height={ mediaImage.height }
										alt={ mediaImage.alt }
									/>
								</div>
							) }
						</>
					) }
					{ blockLayout === 'layout-2' && (
						<div className="media-text-block__content">
							{ showImage && mediaImage && (
								<div
									className="media-text-block__image-wrapper"
								>
									<img
										src={ mediaImage.url }
										width={ mediaImage.width }
										height={ mediaImage.height }
										alt={ mediaImage.alt }
									/>
								</div>
							) }
							{ showDesc && description && (
								<RichText.Content
									tagName="p"
									value={ description }
									className="media-text-block__desc"
									style={ descStyle }
								/>
							) }
						</div>
					) }
				</div>
			</div>
		</div>
	);
}
