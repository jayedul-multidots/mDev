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
		heading,
		description,
		showHeading,
		showDesc,
		contentAlignment,
		headingColor,
		descriptionColor,
		statsNumberColor,
		statsDescColor,
		bgColor,
		listItems,
	} = attributes;

	const classes = classnames( className, 'stats-section' );

	const headingStyle = {};
	headingColor && ( headingStyle.color = headingColor );

	const descStyle = {};
	descriptionColor && ( descStyle.color = descriptionColor );

	const statsNumberStyle = {};
	statsNumberColor && ( statsNumberStyle.color = statsNumberColor );

	const statsDescStyle = {};
	statsDescColor && ( statsDescStyle.color = statsDescColor );

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
				<div
					className={ `stats-section__heading-wrapper ${ contentAlignment }` }
				>
					{ showHeading && heading && (
						<RichText.Content
							tagName="h3"
							value={ heading }
							style={ headingStyle }
							className="stats-section__heading"
						/>
					) }
					{ showDesc && description && (
						<RichText.Content
							className="stats-section__description"
							tagName="p"
							value={ description }
							style={ descStyle }
						/>
					) }
				</div>
				<div className="stats-section__wrapper">
					{ 0 < listItems.length && (
						<>
							{ listItems.map( ( data, index ) => {
								return (
									<div
										className="stats-section__item"
										key={ index }
									>
										{ data.statsNumber && (
											<RichText.Content
												tagName="h4"
												className="stats-section__item-number"
												value={ data.statsNumber }
												style={ statsNumberStyle }
											/>
										) }
										{ data.statsDescription && (
											<RichText.Content
												tagName="p"
												className="stats-section__item-description"
												value={ data.statsDescription }
												style={ statsDescStyle }
											/>
										) }
									</div>
								);
							} ) }
						</>
					) }
				</div>
			</div>
		</div>
	);
}
