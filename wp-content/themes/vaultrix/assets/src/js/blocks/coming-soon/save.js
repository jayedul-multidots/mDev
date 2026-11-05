/* eslint-disable no-unused-expressions */
/* eslint-disable no-nested-ternary */
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { RichText } from '@wordpress/block-editor';

import classnames from 'classnames';

export default function save(props) {
	const { attributes, className } = props;
	const {
		blockID,
		heading,
		showHeading,
		cardItems,
		headingColor,
		DescColor,
		TitleColor,
		button,
		showButton,
		btnStyle,
	} = attributes;

	const headingStyle = {};
	headingColor && (headingStyle.color = headingColor);

	const classes = classnames(className, 'coming-soon-block');

	const TitleStyle = {};
	TitleColor && (TitleStyle.color = TitleColor);

	const DescStyle = {};
	DescColor && (DescStyle.color = DescColor);

	const btnClasses = classnames( btnStyle );

	return (
		<div className={classes} id={blockID}>

				<div className="container">
					<div className="coming-soon-block__head">
					
						{heading && showHeading && (
							<RichText.Content
								className="coming-soon-block__heading"
								tagName="h2"
								value={heading}
								style={headingStyle}
							/>
						)}
					</div>
		
			<div className="coming-soon-block-slider">
	
					<div
						className="coming-soon-block-slider-main slick-slider"
					>
						{cardItems.map((cardItem, index) => {
							return (
								<div
									className="coming-soon-block-slider__item"
									key={index}
								>
									<div className='coming-soon-block__overlay'>
										<span></span>
									</div>
									<div className="coming-soon-block-slider__content">
										{cardItem.imageURL && (
											<div className="coming-soon-block-slider__image">
												<img
													width={cardItem.imageWidth}
													height={
														cardItem.imageHeight
													}
													src={cardItem.imageURL}
													alt={cardItem.imageAlt}
												/>
											</div>
										)}
										<div className='coming-soon-block-slider__details'>
											<div className='coming-soon-block-slider__name'>
											{cardItem.title && (
													<RichText.Content
														tagName="h3"
														className="coming-soon-block-slider__series-name"
														value={cardItem.title}
														style={TitleStyle}
													/>
												)}
												{cardItem.desc && (
													<RichText.Content
														tagName="p"
														className="coming-soon-block-slider__episode-name"
														value={cardItem.desc}
														style={DescStyle}
													/>
												)}
												</div>
										</div>
									</div>
								</div>
							);
						})}
					</div>
		
			</div>

			{ showButton && button && (
						<div className=" coming-soon-block__btn btn-wrap">
							<RichText.Content
								tagName="p"
								className={ btnClasses }
								value={ button }
							/>
						</div>
					) }
	
			</div>
		</div>
	);
}
