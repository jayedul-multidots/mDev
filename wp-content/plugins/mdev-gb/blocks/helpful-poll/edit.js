/**
 * Editor UI for the mdev-gb/helpful-poll block.
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';

import { readCounts } from './lib/counts';

const DEFAULTS = {
	question: __( 'Was this article helpful?', 'mdev-gb' ),
	yesLabel: __( 'Yes', 'mdev-gb' ),
	noLabel: __( 'No', 'mdev-gb' ),
	thanksMessage: __( 'Thanks for your feedback!', 'mdev-gb' ),
};

/**
 * Current counts for the post being edited, read from its meta (exposed to
 * editors only; see src/HelpfulPoll/VoteCounts.php).
 *
 * @param {Object} props          Props.
 * @param {number} props.postId   Post ID.
 * @param {string} props.postType Post type.
 * @return {Element} Results.
 */
function Results( { postId, postType } ) {
	const [ meta ] = useEntityProp( 'postType', postType, 'meta', postId );
	const counts = readCounts( meta );

	if ( ! counts ) {
		return (
			<p>
				{ __(
					'Results are available once the post is saved.',
					'mdev-gb'
				) }
			</p>
		);
	}

	return (
		<ul className="wp-block-mdev-gb-helpful-poll__results">
			<li>
				{ sprintf(
					/* translators: %d: number of "Yes" answers. */
					__( 'Yes: %d', 'mdev-gb' ),
					counts.yes
				) }
			</li>
			<li>
				{ sprintf(
					/* translators: %d: number of "No" answers. */
					__( 'No: %d', 'mdev-gb' ),
					counts.no
				) }
			</li>
		</ul>
	);
}

export default function Edit( { attributes, setAttributes, context } ) {
	const blockProps = useBlockProps();
	const { postId, postType } = context;
	const label = ( key ) => attributes[ key ] || DEFAULTS[ key ];

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'mdev-gb' ) }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Yes button label', 'mdev-gb' ) }
						placeholder={ DEFAULTS.yesLabel }
						value={ attributes.yesLabel }
						onChange={ ( yesLabel ) =>
							setAttributes( { yesLabel } )
						}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'No button label', 'mdev-gb' ) }
						placeholder={ DEFAULTS.noLabel }
						value={ attributes.noLabel }
						onChange={ ( noLabel ) => setAttributes( { noLabel } ) }
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Thank-you message', 'mdev-gb' ) }
						help={ __(
							'Shown after the visitor answers.',
							'mdev-gb'
						) }
						placeholder={ DEFAULTS.thanksMessage }
						value={ attributes.thanksMessage }
						onChange={ ( thanksMessage ) =>
							setAttributes( { thanksMessage } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Results', 'mdev-gb' ) }>
					{ postId && postType ? (
						<Results postId={ postId } postType={ postType } />
					) : (
						<p>
							{ __(
								'Results are shown when editing a post.',
								'mdev-gb'
							) }
						</p>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="p"
					className="wp-block-mdev-gb-helpful-poll__question"
					aria-label={ __( 'Question', 'mdev-gb' ) }
					placeholder={ DEFAULTS.question }
					allowedFormats={ [ 'core/bold', 'core/italic' ] }
					withoutInteractiveFormatting
					value={ attributes.question }
					onChange={ ( question ) => setAttributes( { question } ) }
				/>
				<div className="wp-block-mdev-gb-helpful-poll__buttons">
					{ /* Preview only: votes are recorded on the published post. */ }
					<span className="wp-block-mdev-gb-helpful-poll__button wp-element-button">
						{ label( 'yesLabel' ) }
					</span>
					<span className="wp-block-mdev-gb-helpful-poll__button wp-element-button">
						{ label( 'noLabel' ) }
					</span>
				</div>
			</div>
		</>
	);
}
